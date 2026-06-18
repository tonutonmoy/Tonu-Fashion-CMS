<?php

namespace App\Services\Payments;

use App\Data\Payments\PaymentInitResult;
use App\Data\Payments\PaymentVerifyResult;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NagadGateway extends AbstractPaymentGateway
{
    public function gateway(): string
    {
        return 'nagad';
    }

    protected function config(): array
    {
        return $this->settings->gateway('nagad');
    }

    public function isConfigured(): bool
    {
        $config = $this->config();

        return ($config['enabled'] ?? false)
            && ! empty($config['merchant_id'])
            && ! empty($config['app_key'])
            && ! empty($config['app_secret']);
    }

    public function initiate(Order $order, PaymentTransaction $transaction): PaymentInitResult
    {
        $config = $this->config();
        $orderId = $transaction->transaction_id;
        $datetime = now()->format('YmdHis');
        $challenge = Str::random(40);

        $sensitive = [
            'merchantId' => $config['merchant_id'],
            'datetime' => $datetime,
            'orderId' => $orderId,
            'challenge' => $challenge,
        ];

        $encrypted = $this->encrypt(json_encode($sensitive), $config['app_secret']);

        $response = $this->http()->post('/check-out/initialize/'.$config['merchant_id'].'/'.$orderId, [
            'accountNumber' => $order->customer_phone,
            'dateTime' => $datetime,
            'sensitiveData' => $encrypted['cipher'],
            'signature' => $encrypted['signature'],
        ]);

        $body = $response->json() ?? [];

        if (! $response->successful() || empty($body['callBackUrl'])) {
            Log::warning('Nagad initialize failed', ['body' => $body]);

            return PaymentInitResult::failure($body['message'] ?? 'Nagad payment initiation failed.', $body);
        }

        return PaymentInitResult::redirect($body['callBackUrl'], $orderId, $body);
    }

    public function verify(PaymentTransaction $transaction, array $callbackData = []): PaymentVerifyResult
    {
        $status = strtolower((string) ($callbackData['status'] ?? $callbackData['statusCode'] ?? ''));

        if (in_array($status, ['success', '00', 'approved'], true)) {
            return PaymentVerifyResult::paid($transaction->transaction_id, $callbackData);
        }

        return PaymentVerifyResult::unpaid($callbackData['message'] ?? 'Nagad payment not completed.', $callbackData);
    }

    private function encrypt(string $plain, string $secret): array
    {
        $key = base64_decode($secret);
        $iv = openssl_random_pseudo_bytes(16);
        $cipher = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $payload = base64_encode($iv.$cipher);
        $signature = base64_encode(hash_hmac('sha256', $payload, $key, true));

        return ['cipher' => $payload, 'signature' => $signature];
    }
}
