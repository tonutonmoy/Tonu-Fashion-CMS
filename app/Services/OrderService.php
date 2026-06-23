<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Jobs\CreateCourierParcelJob;
use App\Jobs\SendOrderSmsJob;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private CourierSettingsService $courierSettings,
    ) {}

    public function updateStatus(int $orderId, OrderStatus $status): void
    {
        $order = $this->orders->find($orderId);

        if ($order->status === $status) {
            return;
        }

        $this->forceStatus($orderId, $status);
    }

    public function forceStatus(int $orderId, OrderStatus $status, bool $notify = true): void
    {
        $order = $this->orders->find($orderId);

        if ($order->status === $status) {
            return;
        }

        $this->applyStatus($orderId, $status, $notify);
    }

    private function applyStatus(int $orderId, OrderStatus $status, bool $notify = true): void
    {
        $order = $this->orders->find($orderId);
        $data = ['status' => $status];

        match ($status) {
            OrderStatus::CallingStage, OrderStatus::Payment => $data['confirmed_at'] = $order->confirmed_at ?? now(),
            OrderStatus::Courier => $data['shipped_at'] = $order->shipped_at ?? now(),
            OrderStatus::Delivered => $data['delivered_at'] = now(),
            OrderStatus::Cancelled => $data['cancelled_at'] = now(),
            default => null,
        };

        $this->orders->update($orderId, $data);

        if (! $notify) {
            return;
        }

        match ($status) {
            OrderStatus::CallingStage => SendOrderSmsJob::dispatch($orderId, 'confirmed'),
            OrderStatus::Courier => SendOrderSmsJob::dispatch($orderId, 'parcel_created'),
            OrderStatus::Delivered => SendOrderSmsJob::dispatch($orderId, 'delivered'),
            OrderStatus::Returned => SendOrderSmsJob::dispatch($orderId, 'returned'),
            default => null,
        };

        if ($status === OrderStatus::CallingStage && $this->courierSettings->isAutoParcelEnabled()) {
            CreateCourierParcelJob::dispatch($orderId);
        }
    }
}
