<?php

namespace App\Services\Couriers;

use App\Enums\CourierType;
use App\Models\Order;

class SteadfastCourierGateway extends AbstractCourierGateway
{
    public function type(): string
    {
        return CourierType::Steadfast->value;
    }

    public function isConfigured(): bool
    {
        $config = $this->settings->courier($this->type());

        return ($config['enabled'] ?? false)
            && ! empty($config['api_key'])
            && ! empty($config['secret_key'])
            && ! empty($config['base_url']);
    }

    protected function doCreateParcel(Order $order, array $config): array
    {
        $response = $this->http($config)
            ->withHeaders([
                'Api-Key' => $config['api_key'],
                'Secret-Key' => $config['secret_key'] ?? '',
                'Content-Type' => 'application/json',
            ])
            ->post('/create_order', [
                'invoice' => $order->order_number,
                'recipient_name' => $order->customer_name,
                'recipient_phone' => $this->normalizePhone($order->customer_phone),
                'recipient_address' => $this->buildAddress($order),
                'cod_amount' => (float) $order->total,
                'note' => $order->order_note,
            ]);

        $body = $response->json() ?? [];

        if (! $response->successful() || ! ($body['status'] ?? 0)) {
            return [
                'success' => false,
                'message' => $body['message'] ?? $response->body(),
                'response' => $body,
            ];
        }

        $consignment = $body['consignment'] ?? $body;

        return [
            'success' => true,
            'consignment_id' => (string) ($consignment['consignment_id'] ?? $consignment['id'] ?? ''),
            'tracking_code' => (string) ($consignment['tracking_code'] ?? $consignment['invoice'] ?? $order->order_number),
            'status' => 'created',
            'response' => $body,
        ];
    }

    protected function doFetchStatus(string $consignmentId, ?string $trackingCode, array $config): array
    {
        $response = $this->http($config)
            ->withHeaders([
                'Api-Key' => $config['api_key'],
                'Secret-Key' => $config['secret_key'] ?? '',
            ])
            ->get("/status_by_cid/{$consignmentId}");

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            return ['success' => false, 'message' => $body['message'] ?? $response->body()];
        }

        $deliveryStatus = strtolower((string) ($body['delivery_status'] ?? $body['status'] ?? 'unknown'));

        return [
            'success' => true,
            'status' => $deliveryStatus,
            'description' => $body['message'] ?? null,
            'history' => $this->normalizeHistory($body),
            'response' => $body,
        ];
    }

    private function normalizeHistory(array $body): array
    {
        $entries = $body['tracking_history'] ?? $body['history'] ?? [];

        return collect($entries)->map(fn ($item) => [
            'status' => $item['status'] ?? $item['delivery_status'] ?? 'update',
            'description' => $item['message'] ?? $item['description'] ?? null,
            'recorded_at' => $item['created_at'] ?? $item['time'] ?? now()->toIso8601String(),
        ])->all();
    }
}
