<?php

namespace App\Services;

use App\Enums\CourierType;
use App\Enums\OrderStatus;
use App\Jobs\SendOrderSmsJob;
use App\Models\CourierParcel;
use App\Models\CourierTrackingHistory;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ParcelService
{
    public function __construct(
        private CourierManager $couriers,
        private CourierSettingsService $settings,
        private OrderService $orders,
        private ActivityLogService $activity,
    ) {}

    public function createParcel(Order $order, ?CourierType $courier = null): CourierParcel
    {
        if ($order->hasParcel()) {
            throw new \RuntimeException('A courier parcel already exists for this order.');
        }

        if ($order->status->isTerminal()) {
            throw new \RuntimeException('Cannot create a parcel for a '.$order->status->label().' order.');
        }

        $courier ??= $this->settings->defaultCourier();
        $gateway = $this->couriers->gateway($courier);

        if (! $gateway->isConfigured()) {
            throw new \RuntimeException("{$courier->label()} is not configured or enabled.");
        }

        $order->loadMissing('items');
        $result = $gateway->createParcel($order);

        if (! $result->success) {
            $this->activity->log('courier.parcel_failed', $result->message, $order, [
                'courier' => $courier->value,
                'raw' => $result->raw,
            ]);

            throw new \RuntimeException($result->message ?? 'Failed to create courier parcel.');
        }

        return DB::transaction(function () use ($order, $courier, $result) {
            $parcel = CourierParcel::query()->create([
                'order_id' => $order->id,
                'courier_name' => $courier->value,
                'consignment_id' => $result->consignmentId,
                'tracking_code' => $result->trackingCode,
                'tracking_url' => $result->trackingUrl,
                'current_status' => $result->status ?? 'created',
                'last_synced_at' => now(),
                'payload' => $result->raw,
            ]);

            CourierTrackingHistory::query()->create([
                'courier_parcel_id' => $parcel->id,
                'status' => $parcel->current_status,
                'description' => 'Parcel created with '.$courier->label(),
                'recorded_at' => now(),
                'raw' => $result->raw,
            ]);

            $this->orders->forceStatus($order->id, OrderStatus::Courier, notify: false);

            $this->activity->log('courier.parcel_created', "Parcel created via {$courier->label()}", $order, [
                'tracking_code' => $parcel->tracking_code,
                'courier' => $courier->value,
            ]);

            SendOrderSmsJob::dispatch($order->id, 'parcel_created');

            return $parcel->fresh('histories');
        });
    }

    public function syncParcel(CourierParcel $parcel): CourierParcel
    {
        $gateway = $this->couriers->gateway($parcel->courier_name);
        $result = $gateway->fetchStatus((string) $parcel->consignment_id, $parcel->tracking_code);

        if (! $result->success) {
            $this->activity->log('courier.sync_failed', $result->message, $parcel->order, [
                'parcel_id' => $parcel->id,
                'raw' => $result->raw,
            ]);

            throw new \RuntimeException($result->message ?? 'Failed to sync parcel status.');
        }

        return DB::transaction(function () use ($parcel, $result) {
            $previous = $parcel->current_status;
            $parcel->update([
                'current_status' => $result->status,
                'last_synced_at' => now(),
                'payload' => array_merge($parcel->payload ?? [], ['last_sync' => $result->raw]),
            ]);

            if ($previous !== $result->status) {
                CourierTrackingHistory::query()->create([
                    'courier_parcel_id' => $parcel->id,
                    'status' => $result->status,
                    'description' => $result->description,
                    'recorded_at' => now(),
                    'raw' => $result->raw,
                ]);
            }

            foreach ($result->history as $entry) {
                $exists = $parcel->histories()
                    ->where('status', $entry['status'])
                    ->where('description', $entry['description'] ?? null)
                    ->exists();

                if (! $exists) {
                    CourierTrackingHistory::query()->create([
                        'courier_parcel_id' => $parcel->id,
                        'status' => $entry['status'],
                        'description' => $entry['description'] ?? null,
                        'recorded_at' => $entry['recorded_at'] ?? now(),
                        'raw' => $entry,
                    ]);
                }
            }

            $order = $parcel->order;
            $this->applyOrderStatusFromCourier($order, $result->status);

            $this->activity->log('courier.synced', 'Parcel status synced', $order, [
                'status' => $result->status,
                'tracking_code' => $parcel->tracking_code,
            ]);

            return $parcel->fresh(['histories', 'order']);
        });
    }

    public function syncActiveParcels(): int
    {
        $count = 0;

        CourierParcel::query()
            ->whereNotIn('current_status', ['delivered', 'returned', 'cancelled'])
            ->with('order')
            ->orderBy('id')
            ->chunk(50, function ($parcels) use (&$count) {
                foreach ($parcels as $parcel) {
                    try {
                        $this->syncParcel($parcel);
                        $count++;
                    } catch (\Throwable) {
                        continue;
                    }
                }
            });

        return $count;
    }

    public function findTrackableOrder(string $phone, string $orderNumber): ?Order
    {
        $normalized = preg_replace('/[^0-9]/', '', $phone);

        return Order::query()
            ->with(['courierParcel.histories', 'items'])
            ->where('order_number', $orderNumber)
            ->where(function ($q) use ($phone, $normalized) {
                $q->where('customer_phone', $phone)
                    ->orWhere('customer_phone', $normalized)
                    ->orWhere('customer_phone', 'like', '%'.substr($normalized, -10));
            })
            ->first();
    }

    private function applyOrderStatusFromCourier(Order $order, string $courierStatus, bool $notify = true): void
    {
        $mapped = $this->mapCourierStatusToOrder($courierStatus);

        if (! $mapped || $order->status === $mapped) {
            return;
        }

        $this->orders->forceStatus($order->id, $mapped, notify: $notify && $this->shouldNotify($order->status, $mapped));
    }

    private function shouldNotify(OrderStatus $from, OrderStatus $to): bool
    {
        return match ($to) {
            OrderStatus::Delivered, OrderStatus::Returned => true,
            OrderStatus::Courier => ! in_array($from, [OrderStatus::Courier], true),
            default => false,
        };
    }

    public function mapCourierStatusToOrder(string $courierStatus): ?OrderStatus
    {
        $status = strtolower($courierStatus);

        return match (true) {
            str_contains($status, 'return') => OrderStatus::Returned,
            str_contains($status, 'deliver') && ! str_contains($status, 'undeliver') => OrderStatus::Delivered,
            str_contains($status, 'pick'),
            str_contains($status, 'transit'),
            str_contains($status, 'hub'),
            str_contains($status, 'on the way'),
            str_contains($status, 'ship'),
            str_contains($status, 'dispatch'),
            str_contains($status, 'out for delivery'),
            str_contains($status, 'created'),
            str_contains($status, 'pending'),
            str_contains($status, 'placed') => OrderStatus::Courier,
            default => null,
        };
    }
}
