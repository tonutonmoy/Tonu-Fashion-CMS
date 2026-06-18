<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacebookConversionApiService
{
    public function __construct(private MarketingService $marketing) {}

    public function sendEvent(string $eventName, array $userData, array $customData = [], ?string $eventId = null, ?int $eventTime = null): bool
    {
        if (! $this->marketing->isCapiEnabled()) {
            return false;
        }

        $config = $this->marketing->all();
        $eventId = $eventId ?? (string) Str::uuid();
        $eventTime = $eventTime ?? time();

        $payload = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => $eventTime,
                'event_id' => $eventId,
                'action_source' => 'website',
                'user_data' => $this->buildUserData($userData),
                'custom_data' => $customData,
            ]],
        ];

        if ($config['test_event_code']) {
            $payload['test_event_code'] = $config['test_event_code'];
        }

        $url = sprintf(
            'https://graph.facebook.com/v21.0/%s/events?access_token=%s',
            $config['facebook_dataset_id'],
            $config['facebook_access_token']
        );

        try {
            $response = Http::timeout(15)->post($url, $payload);

            if (! $response->successful()) {
                Log::warning('Facebook CAPI error', ['response' => $response->json()]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Facebook CAPI failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function viewContent(Product $product, array $context): void
    {
        $this->sendEvent('ViewContent', $context, [
            'content_ids' => [$product->sku],
            'content_name' => $product->name,
            'content_type' => 'product',
            'value' => (float) $product->effective_price,
            'currency' => 'BDT',
        ], $context['event_id'] ?? null);
    }

    public function addToCart(array $context, Product $product, int $quantity, float $value): void
    {
        $this->sendEvent('AddToCart', $context, [
            'content_ids' => [$product->sku],
            'content_name' => $product->name,
            'content_type' => 'product',
            'value' => $value,
            'currency' => 'BDT',
            'num_items' => $quantity,
        ], $context['event_id'] ?? null);
    }

    public function initiateCheckout(array $context, float $value, int $numItems): void
    {
        $this->sendEvent('InitiateCheckout', $context, [
            'value' => $value,
            'currency' => 'BDT',
            'num_items' => $numItems,
        ], $context['event_id'] ?? null);
    }

    public function purchase(Order $order, array $context): void
    {
        $eventId = $order->purchase_event_id ?? $context['event_id'] ?? (string) Str::uuid();

        $this->sendEvent('Purchase', array_merge($context, [
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
        ]), [
            'value' => (float) $order->total,
            'currency' => 'BDT',
            'content_ids' => $order->items->pluck('product_sku')->toArray(),
            'content_type' => 'product',
            'num_items' => $order->items->sum('quantity'),
            'order_id' => $order->order_number,
        ], $eventId);
    }

    private function buildUserData(array $data): array
    {
        $user = [];

        if (! empty($data['email'])) {
            $user['em'] = [hash('sha256', strtolower(trim($data['email'])))];
        }
        if (! empty($data['phone'])) {
            $digits = preg_replace('/[^0-9]/', '', $data['phone']);
            if (str_starts_with($digits, '01')) {
                $digits = '88'.$digits;
            }
            $user['ph'] = [hash('sha256', $digits)];
        }
        if (! empty($data['client_ip_address'])) {
            $user['client_ip_address'] = $data['client_ip_address'];
        }
        if (! empty($data['client_user_agent'])) {
            $user['client_user_agent'] = $data['client_user_agent'];
        }
        if (! empty($data['fbp'])) {
            $user['fbp'] = $data['fbp'];
        }
        if (! empty($data['fbc'])) {
            $user['fbc'] = $data['fbc'];
        }

        return $user;
    }
}
