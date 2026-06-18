<?php

namespace App\Services\Couriers;

use App\Enums\CourierType;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class PathaoCourierGateway extends AbstractCourierGateway
{
    public function type(): string
    {
        return CourierType::Pathao->value;
    }

    protected function doCreateParcel(Order $order, array $config): array
    {
        $token = $this->accessToken($config);

        $response = $this->http($config)
            ->withToken($token)
            ->post('/aladdin/api/v1/orders', [
                'store_id' => $config['store_id'] ?? null,
                'merchant_order_id' => $order->order_number,
                'recipient_name' => $order->customer_name,
                'recipient_phone' => $this->normalizePhone($order->customer_phone),
                'recipient_address' => $this->buildAddress($order),
                'recipient_city' => $order->shipping_district,
                'recipient_zone' => $order->shipping_area ?? $order->shipping_district,
                'delivery_type' => 48,
                'item_type' => 2,
                'item_quantity' => $order->items->sum('quantity'),
                'item_weight' => 0.5,
                'amount_to_collect' => (int) $order->total,
                'item_description' => 'Fashion items — '.$order->order_number,
            ]);

        $body = $response->json() ?? [];

        if (! $response->successful() || ($body['type'] ?? '') === 'error') {
            return [
                'success' => false,
                'message' => $body['message'] ?? $response->body(),
                'response' => $body,
            ];
        }

        $data = $body['data'] ?? $body;

        return [
            'success' => true,
            'consignment_id' => (string) ($data['consignment_id'] ?? $data['order_id'] ?? ''),
            'tracking_code' => (string) ($data['consignment_id'] ?? $data['merchant_order_id'] ?? $order->order_number),
            'status' => strtolower((string) ($data['order_status'] ?? 'created')),
            'response' => $body,
        ];
    }

    protected function doFetchStatus(string $consignmentId, ?string $trackingCode, array $config): array
    {
        $token = $this->accessToken($config);

        $response = $this->http($config)
            ->withToken($token)
            ->get("/aladdin/api/v1/orders/{$consignmentId}/info");

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            return ['success' => false, 'message' => $body['message'] ?? $response->body()];
        }

        $data = $body['data'] ?? $body;
        $status = strtolower((string) ($data['order_status'] ?? $data['order_status_slug'] ?? 'unknown'));

        return [
            'success' => true,
            'status' => $status,
            'description' => $data['order_status'] ?? null,
            'history' => collect($data['tracking'] ?? [])->map(fn ($item) => [
                'status' => $item['status'] ?? 'update',
                'description' => $item['message'] ?? null,
                'recorded_at' => $item['time'] ?? now()->toIso8601String(),
            ])->all(),
            'response' => $body,
        ];
    }

    private function accessToken(array $config): string
    {
        return Cache::remember('pathao_courier_token', 3500, function () use ($config) {
            $response = $this->http($config)->post('/aladdin/api/v1/issue-token', [
                'client_id' => $config['api_key'],
                'client_secret' => $config['secret_key'] ?? '',
                'grant_type' => 'password',
                'username' => $config['username'] ?? '',
                'password' => $config['password'] ?? '',
            ]);

            $body = $response->json() ?? [];

            if (! $response->successful()) {
                throw new \RuntimeException($body['message'] ?? 'Pathao token request failed.');
            }

            return (string) ($body['access_token'] ?? $body['data']['access_token'] ?? '');
        });
    }
}
