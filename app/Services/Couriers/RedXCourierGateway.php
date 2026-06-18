<?php

namespace App\Services\Couriers;

use App\Enums\CourierType;
use App\Models\Order;

class RedXCourierGateway extends AbstractCourierGateway
{
    public function type(): string
    {
        return CourierType::RedX->value;
    }

    protected function doCreateParcel(Order $order, array $config): array
    {
        $response = $this->http($config)
            ->withHeaders([
                'API-ACCESS-TOKEN' => $config['api_key'],
                'Content-Type' => 'application/json',
            ])
            ->post('/parcel', [
                'customer_name' => $order->customer_name,
                'customer_phone' => $this->normalizePhone($order->customer_phone),
                'delivery_area' => $order->shipping_area ?? $order->shipping_district,
                'delivery_area_id' => $config['delivery_area_id'] ?? null,
                'customer_address' => $this->buildAddress($order),
                'merchant_invoice_id' => $order->order_number,
                'cash_collection_amount' => (float) $order->total,
                'parcel_weight' => 500,
                'instruction' => $order->order_note,
                'value' => (float) $order->total,
            ]);

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $body['message'] ?? $response->body(),
                'response' => $body,
            ];
        }

        $data = $body['data'] ?? $body;
        $trackingId = (string) ($data['tracking_id'] ?? $data['tracking_code'] ?? '');

        if ($trackingId === '') {
            return ['success' => false, 'message' => 'RedX did not return a tracking ID.', 'response' => $body];
        }

        return [
            'success' => true,
            'consignment_id' => (string) ($data['parcel_id'] ?? $trackingId),
            'tracking_code' => $trackingId,
            'status' => strtolower((string) ($data['status'] ?? 'created')),
            'response' => $body,
        ];
    }

    protected function doFetchStatus(string $consignmentId, ?string $trackingCode, array $config): array
    {
        $id = $trackingCode ?: $consignmentId;

        $response = $this->http($config)
            ->withHeaders(['API-ACCESS-TOKEN' => $config['api_key']])
            ->get("/parcel/{$id}");

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            return ['success' => false, 'message' => $body['message'] ?? $response->body()];
        }

        $data = $body['data'] ?? $body;
        $status = strtolower((string) ($data['status'] ?? $data['delivery_status'] ?? 'unknown'));

        return [
            'success' => true,
            'status' => $status,
            'description' => $data['status'] ?? null,
            'history' => collect($data['tracking_history'] ?? [])->map(fn ($item) => [
                'status' => $item['status'] ?? 'update',
                'description' => $item['message'] ?? null,
                'recorded_at' => $item['time'] ?? now()->toIso8601String(),
            ])->all(),
            'response' => $body,
        ];
    }
}
