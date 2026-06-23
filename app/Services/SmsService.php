<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\Sms\SmsNetBdClient;
use App\Services\Sms\SmsSendResult;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function __construct(
        private SettingRepositoryInterface $settings,
        private SmsNetBdClient $client,
    ) {}

    public function settings(): array
    {
        $apiKey = (string) $this->settings->get('sms', 'sms_api_key', '');

        return [
            'sms_provider' => 'sms.net.bd',
            'sms_enabled' => (bool) $this->settings->get('sms', 'sms_enabled', false),
            'sms_api_key_set' => $apiKey !== '',
            'sms_api_key_preview' => $this->maskApiKey($apiKey),
            'sms_sender_id' => $this->settings->get('sms', 'sms_sender_id', ''),
            'notify_confirmed' => (bool) $this->settings->get('sms', 'notify_confirmed', true),
            'notify_shipped' => (bool) $this->settings->get('sms', 'notify_shipped', true),
            'notify_delivered' => (bool) $this->settings->get('sms', 'notify_delivered', true),
            'notify_parcel_created' => (bool) $this->settings->get('sms', 'notify_parcel_created', true),
            'notify_returned' => (bool) $this->settings->get('sms', 'notify_returned', true),
        ];
    }

    public function updateSettings(array $data): void
    {
        if (empty($data['sms_api_key'])) {
            unset($data['sms_api_key']);
        }

        unset($data['sms_api_key_set'], $data['sms_provider']);

        $booleans = [
            'sms_enabled',
            'notify_confirmed',
            'notify_shipped',
            'notify_delivered',
            'notify_parcel_created',
            'notify_returned',
        ];

        $payload = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $booleans, true)) {
                $payload[$key] = ['value' => $value ? '1' : '0', 'type' => 'boolean'];
            } else {
                $payload[$key] = $value;
            }
        }

        $this->settings->setMany('sms', $payload);
    }

    public function isConfigured(): bool
    {
        $settings = $this->settings();

        return $settings['sms_enabled'] && $settings['sms_api_key_set'];
    }

    public function apiKey(): string
    {
        return (string) $this->settings->get('sms', 'sms_api_key', '');
    }

    public function send(string $phone, string $message, bool $force = false): SmsSendResult
    {
        if (! $force && ! $this->isConfigured()) {
            Log::info('SMS skipped (disabled or not configured)', compact('phone'));

            return new SmsSendResult(false, 405, 'SMS is disabled or API key is missing.');
        }

        if ($this->apiKey() === '') {
            return new SmsSendResult(false, 405, 'SMS API key is not configured.');
        }

        $config = $this->settings();
        $senderId = trim((string) ($config['sms_sender_id'] ?? '')) ?: null;

        $result = $this->client->send(
            $this->apiKey(),
            $phone,
            $message,
            $senderId,
        );

        if ($result->success) {
            Log::info('SMS sent', [
                'phone' => $phone,
                'request_id' => $result->requestId,
            ]);
        }

        return $result;
    }

    public function balance(): SmsSendResult
    {
        if ($this->apiKey() === '') {
            return new SmsSendResult(false, 405, 'SMS API key is not configured.');
        }

        return $this->client->balance($this->apiKey());
    }

    public function report(int $requestId): SmsSendResult
    {
        return $this->client->report($this->apiKey(), $requestId);
    }

    public function sendOrderConfirmed(Order $order): ?SmsSendResult
    {
        if (! $this->settings()['notify_confirmed'] || ! $this->isConfigured()) {
            return null;
        }

        return $this->send(
            $order->customer_phone,
            "Order {$order->order_number} confirmed! Total: ".format_bdt($order->total).'. Thank you for shopping with us.',
            force: true,
        );
    }

    public function sendOrderShipped(Order $order): ?SmsSendResult
    {
        if (! $this->settings()['notify_shipped'] || ! $this->isConfigured()) {
            return null;
        }

        $tracking = $order->courierParcel?->tracking_code;
        $message = "Your order {$order->order_number} has been shipped.";
        if ($tracking) {
            $message .= " Tracking: {$tracking}.";
        }

        return $this->send($order->customer_phone, $message, force: true);
    }

    public function sendParcelCreated(Order $order): ?SmsSendResult
    {
        if (! $this->settings()['notify_parcel_created'] || ! $this->isConfigured()) {
            return null;
        }

        $parcel = $order->courierParcel;
        $tracking = $parcel?->tracking_code ?? 'pending';

        return $this->send(
            $order->customer_phone,
            "Parcel created for order {$order->order_number}. Courier: ".ucfirst($parcel?->courier_name ?? 'courier').". Tracking: {$tracking}.",
            force: true,
        );
    }

    public function sendOrderReturned(Order $order): ?SmsSendResult
    {
        if (! $this->settings()['notify_returned'] || ! $this->isConfigured()) {
            return null;
        }

        return $this->send(
            $order->customer_phone,
            "Order {$order->order_number} was returned to sender. Contact us if you need help.",
            force: true,
        );
    }

    public function sendOrderDelivered(Order $order): ?SmsSendResult
    {
        if (! $this->settings()['notify_delivered'] || ! $this->isConfigured()) {
            return null;
        }

        return $this->send(
            $order->customer_phone,
            "Order {$order->order_number} delivered. Thank you! Please rate your purchase.",
            force: true,
        );
    }

    private function maskApiKey(string $key): string
    {
        if ($key === '') {
            return '';
        }

        $length = strlen($key);
        if ($length <= 8) {
            return str_repeat('•', $length);
        }

        return substr($key, 0, 4).str_repeat('•', min(24, $length - 8)).substr($key, -4);
    }
}
