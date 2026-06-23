<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AdminNotificationService
{
    public function __construct(
        private InventoryService $inventory,
    ) {}

    public function lowStockHash(Collection $items): string
    {
        return md5($items->pluck('id')->sort()->implode(','));
    }

    public function unreadLowStockCount(User $user): int
    {
        return $this->unreadLowStockCountFromSummary($this->inventory->summary(), $user);
    }

    public function unreadLowStockCountFromSummary(array $inventory, User $user): int
    {
        $items = collect($inventory['low_stock_products'] ?? []);
        if ($items->isEmpty()) {
            return 0;
        }

        $hash = $this->lowStockHash($items);

        return $user->low_stock_alerts_seen_hash === $hash ? 0 : $items->count();
    }

    public function markLowStockRead(User $user): void
    {
        $items = collect($this->inventory->summary()['low_stock_products']);
        $user->forceFill([
            'low_stock_alerts_seen_hash' => $items->isEmpty() ? null : $this->lowStockHash($items),
        ])->save();

        Cache::forget("admin.header.notifications.{$user->id}");
    }

    public function payload(User $user): array
    {
        $inventory = $this->inventory->summary();
        $items = collect($inventory['low_stock_products']);
        $unread = $this->unreadLowStockCount($user);

        return [
            'low_stock' => [
                'unread_count' => $unread,
                'total_count' => $inventory['low_stock_count'],
                'threshold' => $this->inventory->lowStockThreshold(),
                'items' => $items->values()->all(),
            ],
        ];
    }
}
