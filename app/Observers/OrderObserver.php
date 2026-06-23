<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\InventoryService;
use App\Services\ReportService;

class OrderObserver
{
    public function __construct(
        private InventoryService $inventory,
        private ReportService $reports,
    ) {}

    public function updating(Order $order): void
    {
        if (! $order->isDirty('status')) {
            return;
        }

        $previous = $this->resolveStatus($order->getOriginal('status'));
        $next = $this->resolveStatus($order->status);

        if ($previous === null || $next === null || $previous === $next) {
            return;
        }

        $wasPayment = $previous === OrderStatus::Payment;
        $willPayment = $next === OrderStatus::Payment;

        if (! $wasPayment && $willPayment) {
            $order->cogs = $this->reports->calculateOrderCogs($order);
            $this->inventory->settleForOrder($order);
            $order->payment_at = $order->payment_at ?? now();

            return;
        }

        if ($wasPayment && ! $willPayment) {
            $this->inventory->unsettleForOrder($order);
            $order->inventory_settled = false;
            $order->cogs = 0;
            $order->payment_at = null;

            return;
        }

        if (in_array($next, [OrderStatus::Cancelled, OrderStatus::Returned], true)
            && ! in_array($previous, [OrderStatus::Cancelled, OrderStatus::Returned], true)
            && ! $wasPayment) {
            $this->inventory->rollbackForOrder($order);
        }
    }

    private function resolveStatus(mixed $value): ?OrderStatus
    {
        if ($value instanceof OrderStatus) {
            return $value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        return OrderStatus::tryFrom((string) $value)
            ?? OrderStatus::tryFromLegacy((string) $value);
    }
}
