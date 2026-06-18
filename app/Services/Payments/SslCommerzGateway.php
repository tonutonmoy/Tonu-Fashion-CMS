<?php

namespace App\Services\Payments;

use App\Data\Payments\PaymentInitResult;
use App\Data\Payments\PaymentVerifyResult;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzGateway extends AbstractPaymentGateway
{
    public function gateway(): string
    {
        return 'sslcommerz';
    }

    protected function config(): array
    {
        return $this->settings->gateway('sslcommerz');
    }

    public function initiate(Order $order, PaymentTransaction $transaction): PaymentInitResult
    {
        $config = $this->config();
        $base = rtrim($config['base_url'], '/');

        $payload = [
            'store_id' => $config['store_id'] ?? $config['app_key'],
            'store_passwd' => $config['app_secret'],
            'total_amount' => $order->total,
            'currency' => 'BDT',
            'tran_id' => $transaction->transaction_id,
            'success_url' => route('payments.callback', ['gateway' => 'sslcommerz', 'status' => 'success']),
            'fail_url' => route('payments.callback', ['gateway' => 'sslcommerz', 'status' => 'fail']),
            'cancel_url' => route('payments.callback', ['gateway' => 'sslcommerz', 'status' => 'cancel']),
            'ipn_url' => route('payments.ipn', ['gateway' => 'sslcommerz']),
            'cus_name' => $order->customer_name,
            'cus_phone' => $order->customer_phone,
            'cus_email' => $order->customer_email ?? 'customer@fashionbd.com',
            'cus_add1' => $order->shipping_address,
            'cus_city' => $order->shipping_district,
            'cus_country' => 'Bangladesh',
            'shipping_method' => 'NO',
            'product_name' => 'Fashion BD Order',
            'product_category' => 'Fashion',
            'product_profile' => 'general',
        ];

        $response = Http::timeout(30)->asForm()->post("{$base}/gwprocess/v4/api.php", $payload);
        $body = $response->json() ?? [];

        if (($body['status'] ?? '') !== 'SUCCESS' || empty($body['GatewayPageURL'])) {
            Log::warning('SSLCommerz session failed', ['body' => $body]);

            return PaymentInitResult::failure($body['failedreason'] ?? 'SSLCommerz session creation failed.', $body);
        }

        return PaymentInitResult::redirect($body['GatewayPageURL'], $body['sessionkey'] ?? null, $body);
    }

    public function verify(PaymentTransaction $transaction, array $callbackData = []): PaymentVerifyResult
    {
        $config = $this->config();
        $valId = $callbackData['val_id'] ?? null;

        if (! $valId) {
            $status = strtolower((string) ($callbackData['status'] ?? ''));

            return in_array($status, ['cancel', 'fail', 'failed'], true)
                ? PaymentVerifyResult::unpaid('Payment cancelled or failed.', $callbackData)
                : PaymentVerifyResult::failure('Missing SSLCommerz validation ID.');
        }

        $base = rtrim($config['base_url'], '/');
        $response = Http::timeout(30)->get("{$base}/validator/api/validationserverAPI.php", [
            'val_id' => $valId,
            'store_id' => $config['store_id'] ?? $config['app_key'],
            'store_passwd' => $config['app_secret'],
            'format' => 'json',
        ]);

        $body = $response->json() ?? [];

        if (($body['status'] ?? '') === 'VALID' || ($body['status'] ?? '') === 'VALIDATED') {
            return PaymentVerifyResult::paid($valId, $body);
        }

        return PaymentVerifyResult::unpaid($body['status'] ?? 'Payment not validated.', $body);
    }
}
