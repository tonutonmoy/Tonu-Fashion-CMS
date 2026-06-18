<?php

namespace App\Services\Payments;

use App\Data\Payments\PaymentInitResult;
use App\Data\Payments\PaymentVerifyResult;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BkashGateway extends AbstractPaymentGateway
{
    public function gateway(): string
    {
        return 'bkash';
    }

    protected function config(): array
    {
        return $this->settings->gateway('bkash');
    }

    public function initiate(Order $order, PaymentTransaction $transaction): PaymentInitResult
    {
        $token = $this->token();
        $callback = route('payments.callback', ['gateway' => 'bkash']);

        $response = $this->http()
            ->withToken($token)
            ->post('/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $order->customer_phone,
                'callbackURL' => $callback,
                'amount' => (string) $order->total,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $transaction->transaction_id,
            ]);

        $body = $response->json() ?? [];

        if (! $response->successful() || empty($body['paymentID'])) {
            Log::warning('bKash create payment failed', ['body' => $body]);

            return PaymentInitResult::failure($body['statusMessage'] ?? 'bKash payment initiation failed.', $body);
        }

        return PaymentInitResult::redirect(
            $body['bkashURL'] ?? $body['redirectURL'] ?? '',
            $body['paymentID'],
            $body
        );
    }

    public function verify(PaymentTransaction $transaction, array $callbackData = []): PaymentVerifyResult
    {
        $paymentId = $callbackData['paymentID'] ?? $transaction->gateway_payment_id;

        if (! $paymentId) {
            return PaymentVerifyResult::failure('Missing bKash payment ID.');
        }

        $token = $this->token();

        $execute = $this->http()
            ->withToken($token)
            ->post('/tokenized/checkout/execute', ['paymentID' => $paymentId]);

        $body = $execute->json() ?? [];

        if (($body['transactionStatus'] ?? '') === 'Completed') {
            return PaymentVerifyResult::paid($paymentId, $body);
        }

        $query = $this->http()
            ->withToken($token)
            ->get("/tokenized/checkout/payment/status/{$paymentId}");

        $statusBody = $query->json() ?? [];

        if (($statusBody['transactionStatus'] ?? '') === 'Completed') {
            return PaymentVerifyResult::paid($paymentId, $statusBody);
        }

        return PaymentVerifyResult::unpaid($body['statusMessage'] ?? 'Payment not completed.', $statusBody ?: $body);
    }

    private function token(): string
    {
        $config = $this->config();
        $cacheKey = 'bkash_token_'.md5($config['app_key']);

        return Cache::remember($cacheKey, 3000, function () use ($config) {
            $response = $this->http()
                ->withHeaders([
                    'username' => $config['username'] ?? $config['app_key'],
                    'password' => $config['password'] ?? $config['app_secret'],
                ])
                ->post('/tokenized/checkout/token/grant', [
                    'app_key' => $config['app_key'],
                    'app_secret' => $config['app_secret'],
                ]);

            $body = $response->json() ?? [];

            if (! $response->successful() || empty($body['id_token'])) {
                throw new \RuntimeException($body['statusMessage'] ?? 'bKash token grant failed.');
            }

            return $body['id_token'];
        });
    }
}
