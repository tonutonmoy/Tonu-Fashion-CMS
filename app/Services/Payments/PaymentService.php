<?php

namespace App\Services\Payments;

use App\Data\Payments\PaymentInitResult;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\MarketingEventService;
use App\Services\OrderService;
use App\Services\PaymentSettingsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private PaymentManager $payments,
        private PaymentSettingsService $settings,
        private OrderRepositoryInterface $orders,
        private OrderService $orderService,
        private MarketingEventService $marketing,
    ) {}

    public function initiateOnlinePayment(Order $order): PaymentInitResult
    {
        if (! $order->payment_method->isOnline()) {
            return PaymentInitResult::failure('Order is not an online payment order.');
        }

        $transaction = PaymentTransaction::query()->create([
            'order_id' => $order->id,
            'gateway' => $order->payment_method->value,
            'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
            'amount' => $order->total,
            'currency' => 'BDT',
            'status' => PaymentStatus::Pending,
        ]);

        $gateway = $this->payments->gatewayForMethod($order->payment_method);
        $result = $gateway->initiate($order, $transaction);

        $transaction->update([
            'gateway_payment_id' => $result->gatewayPaymentId,
            'request_payload' => $result->raw,
            'response_payload' => $result->raw,
            'status' => $result->success ? PaymentStatus::Pending : PaymentStatus::Failed,
        ]);

        if (! $result->success) {
            return $result;
        }

        return $result;
    }

    public function handleCallback(PaymentMethod $method, array $data): Order
    {
        $gateway = $this->payments->gatewayForMethod($method);
        $transaction = $this->resolveTransaction($method, $data);

        if (! $transaction) {
            throw new \RuntimeException('Payment transaction not found.');
        }

        $result = $gateway->verify($transaction, $data);

        $transaction->update([
            'response_payload' => array_merge($transaction->response_payload ?? [], $result->raw),
            'gateway_payment_id' => $result->gatewayPaymentId ?? $transaction->gateway_payment_id,
            'status' => $result->paid ? PaymentStatus::Paid : ($result->success ? PaymentStatus::Pending : PaymentStatus::Failed),
            'paid_at' => $result->paid ? now() : null,
        ]);

        if ($result->paid) {
            return $this->markOrderPaid($transaction->order);
        }

        if (! $result->success) {
            $this->orders->update($transaction->order_id, [
                'payment_status' => PaymentStatus::Failed->value,
            ]);
        }

        return $transaction->order->fresh();
    }

    public function markOrderPaid(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $this->orders->update($order->id, [
                'payment_status' => PaymentStatus::Paid->value,
            ]);

            if ($order->status === OrderStatus::Pending) {
                $this->orderService->forceStatus($order->id, OrderStatus::Confirmed);
            }

            $order = $order->fresh()->load('items');
            $this->marketing->trackPurchase($order);

            return $order;
        });
    }

    private function resolveTransaction(PaymentMethod $method, array $data): ?PaymentTransaction
    {
        $txnId = $data['tran_id'] ?? $data['transaction_id'] ?? $data['merchantInvoiceNumber'] ?? null;
        $paymentId = $data['paymentID'] ?? $data['payment_id'] ?? null;

        $query = PaymentTransaction::query()->where('gateway', $method->value);

        if ($txnId) {
            $transaction = $query->where('transaction_id', $txnId)->first();
            if ($transaction) {
                return $transaction;
            }

            $order = Order::query()->where('order_number', $txnId)->first();
            if ($order) {
                return $query->where('order_id', $order->id)->latest()->first();
            }

            return null;
        }

        if ($paymentId) {
            return $query->where('gateway_payment_id', $paymentId)->latest()->first();
        }

        return null;
    }
}
