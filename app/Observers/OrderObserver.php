<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\InventoryService;

class OrderObserver
{
    public function __construct(
        private InventoryService $inventory,
    ) {}

    public function updating(Order $order): void
    {
        if (! $order->isDirty('status')) {
            return;
        }

        $previous = OrderStatus::tryFrom((string) $order->getOriginal('status'))
            ?? OrderStatus::tryFromLegacy((string) $order->getOriginal('status'));

        $next = $order->status instanceof OrderStatus
            ? $order->status
            : OrderStatus::from((string) $order->status);

        if ($previous === null || $previous === $next) {
            return;
        }

        if ($next === OrderStatus::Courier && $previous !== OrderStatus::Courier) {
            $this->inventory->deductForOrder($order);

            return;
        }

        if (in_array($next, [OrderStatus::Cancelled, OrderStatus::Returned], true)
            && ! in_array($previous, [OrderStatus::Cancelled, OrderStatus::Returned], true)) {
            $this->inventory->rollbackForOrder($order);
        }
    }
}
