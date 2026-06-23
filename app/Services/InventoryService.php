<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public const LOW_STOCK_THRESHOLD = 10;

    public function lowStockThreshold(): int
    {
        return max(1, (int) setting('low_stock_threshold', self::LOW_STOCK_THRESHOLD));
    }

    public function reserve(string $variantId, string $orderId, int $quantity): void
    {
        $this->assertPositiveQuantity($quantity);

        try {
            $variant = ProductVariant::query()->findOrFail($variantId);
            $available = $this->availableUnits($variant);

            if ($available < $quantity) {
                throw InsufficientStockException::forVariant($variantId, $quantity, $available);
            }

            $variant->reserved_stock = (int) ($variant->reserved_stock ?? 0) + $quantity;
            $variant->save();

            $this->recordMovement([
                'product_variant_id' => (string) $variantId,
                'product_id' => (string) $variant->product_id,
                'order_id' => (string) $orderId,
                'type' => StockMovementType::Reserve,
                'quantity' => $quantity,
                'note' => 'Reserved for pending order',
            ]);
            $this->bustInventoryCache();
        } catch (InsufficientStockException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not reserve stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function reserveProduct(string $productId, string $orderId, int $quantity): void
    {
        $this->assertPositiveQuantity($quantity);

        try {
            $product = Product::query()->findOrFail($productId);
            $available = $this->availableUnits($product);

            if ($available < $quantity) {
                throw InsufficientStockException::forVariant($productId, $quantity, $available);
            }

            $product->reserved_stock = (int) ($product->reserved_stock ?? 0) + $quantity;
            $product->save();

            $this->recordMovement([
                'product_id' => (string) $productId,
                'order_id' => (string) $orderId,
                'type' => StockMovementType::Reserve,
                'quantity' => $quantity,
                'note' => 'Reserved for pending order (no variant)',
            ]);
        } catch (InsufficientStockException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not reserve stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function deduct(string $variantId, string $orderId, int $quantity): void
    {
        $this->assertPositiveQuantity($quantity);

        try {
            $variant = ProductVariant::query()->findOrFail($variantId);
            $reserved = (int) ($variant->reserved_stock ?? 0);

            if ($reserved < $quantity) {
                throw new \RuntimeException("Cannot deduct {$quantity} units — only {$reserved} reserved.");
            }

            if ((int) $variant->stock < $quantity) {
                throw new \RuntimeException("Cannot deduct {$quantity} units — stock is {$variant->stock}.");
            }

            $variant->reserved_stock = $reserved - $quantity;
            $variant->stock = (int) $variant->stock - $quantity;
            $variant->save();

            $this->recordMovement([
                'product_variant_id' => (string) $variantId,
                'product_id' => (string) $variant->product_id,
                'order_id' => (string) $orderId,
                'type' => StockMovementType::Deduct,
                'quantity' => $quantity,
                'note' => 'Deducted when order sent to courier',
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not deduct stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function deductProduct(string $productId, string $orderId, int $quantity): void
    {
        $this->assertPositiveQuantity($quantity);

        try {
            $product = Product::query()->findOrFail($productId);
            $reserved = (int) ($product->reserved_stock ?? 0);

            if ($reserved < $quantity) {
                throw new \RuntimeException("Cannot deduct {$quantity} units — only {$reserved} reserved.");
            }

            if ((int) $product->stock < $quantity) {
                throw new \RuntimeException("Cannot deduct {$quantity} units — stock is {$product->stock}.");
            }

            $product->reserved_stock = $reserved - $quantity;
            $product->stock = (int) $product->stock - $quantity;
            $product->save();

            $this->recordMovement([
                'product_id' => (string) $productId,
                'order_id' => (string) $orderId,
                'type' => StockMovementType::Deduct,
                'quantity' => $quantity,
                'note' => 'Deducted when order sent to courier',
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not deduct stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function rollback(string $variantId, string $orderId, int $quantity): void
    {
        $this->assertPositiveQuantity($quantity);

        try {
            $variant = ProductVariant::query()->findOrFail($variantId);
            $reserved = (int) ($variant->reserved_stock ?? 0);

            if ($reserved >= $quantity) {
                $variant->reserved_stock = $reserved - $quantity;
                $variant->save();

                $this->recordMovement([
                    'product_variant_id' => (string) $variantId,
                    'product_id' => (string) $variant->product_id,
                    'order_id' => (string) $orderId,
                    'type' => StockMovementType::Rollback,
                    'quantity' => $quantity,
                    'note' => 'Reservation released (cancel/return)',
                ]);

                return;
            }

            $variant->stock = (int) $variant->stock + $quantity;
            if ($reserved > 0) {
                $variant->reserved_stock = 0;
            }
            $variant->save();

            $this->recordMovement([
                'product_variant_id' => (string) $variantId,
                'product_id' => (string) $variant->product_id,
                'order_id' => (string) $orderId,
                'type' => StockMovementType::Rollback,
                'quantity' => $quantity,
                'note' => 'Stock restored after cancel/return (post-deduct)',
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not rollback stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function rollbackProduct(string $productId, string $orderId, int $quantity): void
    {
        $this->assertPositiveQuantity($quantity);

        try {
            $product = Product::query()->findOrFail($productId);
            $reserved = (int) ($product->reserved_stock ?? 0);

            if ($reserved >= $quantity) {
                $product->reserved_stock = $reserved - $quantity;
                $product->save();

                $this->recordMovement([
                    'product_id' => (string) $productId,
                    'order_id' => (string) $orderId,
                    'type' => StockMovementType::Rollback,
                    'quantity' => $quantity,
                    'note' => 'Reservation released (cancel/return)',
                ]);

                return;
            }

            $product->stock = (int) $product->stock + $quantity;
            if ($reserved > 0) {
                $product->reserved_stock = 0;
            }
            $product->save();

            $this->recordMovement([
                'product_id' => (string) $productId,
                'order_id' => (string) $orderId,
                'type' => StockMovementType::Rollback,
                'quantity' => $quantity,
                'note' => 'Stock restored after cancel/return (post-deduct)',
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not rollback stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function adjust(string $variantId, int $quantity, string $note, ?string $adminId = null): void
    {
        if ($quantity === 0) {
            throw new \InvalidArgumentException('Adjustment quantity cannot be zero.');
        }

        try {
            $variant = ProductVariant::query()->findOrFail($variantId);

            if ($quantity > 0) {
                $variant->stock = (int) $variant->stock + $quantity;
                $type = StockMovementType::AdjustIn;
            } else {
                $remove = abs($quantity);
                $available = $this->availableUnits($variant);

                if ($available < $remove) {
                    throw InsufficientStockException::forVariant($variantId, $remove, $available);
                }

                $variant->stock = (int) $variant->stock - $remove;
                $type = StockMovementType::AdjustOut;
            }

            $variant->save();

            $this->recordMovement([
                'product_variant_id' => (string) $variantId,
                'product_id' => (string) $variant->product_id,
                'type' => $type,
                'quantity' => abs($quantity),
                'note' => $note,
                'admin_id' => $adminId,
            ]);
        } catch (InsufficientStockException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not adjust stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function adjustProduct(string $productId, int $quantity, string $note, ?string $adminId = null): void
    {
        if ($quantity === 0) {
            throw new \InvalidArgumentException('Adjustment quantity cannot be zero.');
        }

        try {
            $product = Product::query()->findOrFail($productId);

            if ($quantity > 0) {
                $product->stock = (int) $product->stock + $quantity;
                $type = StockMovementType::AdjustIn;
            } else {
                $remove = abs($quantity);
                $available = $this->availableUnits($product);

                if ($available < $remove) {
                    throw InsufficientStockException::forVariant($productId, $remove, $available);
                }

                $product->stock = (int) $product->stock - $remove;
                $type = StockMovementType::AdjustOut;
            }

            $product->save();

            $this->recordMovement([
                'product_id' => (string) $productId,
                'type' => $type,
                'quantity' => abs($quantity),
                'note' => $note,
                'admin_id' => $adminId,
            ]);
        } catch (InsufficientStockException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Could not adjust stock: '.$e->getMessage(), 0, $e);
        }
    }

    public function getAvailableStock(string $variantId): int
    {
        $variant = ProductVariant::query()->find($variantId);

        return $variant ? $this->availableUnits($variant) : 0;
    }

    public function getAvailableProductStock(string $productId): int
    {
        $product = Product::query()->find($productId);

        return $product ? $this->availableUnits($product) : 0;
    }

    public function reserveOrder(Order $order): void
    {
        if ($order->status !== OrderStatus::Pending) {
            return;
        }

        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $this->reserveOrderItem($item, (string) $order->id);
        }
    }

    public function deductForOrder(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $this->deductOrderItem($item, (string) $order->id);
        }
    }

    public function rollbackForOrder(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $this->rollbackOrderItem($item, (string) $order->id);
        }
    }

    public function settleForOrder(Order $order): void
    {
        if ($order->inventory_settled || $this->orderHasDeduct((string) $order->id)) {
            $order->inventory_settled = true;

            return;
        }

        $this->deductForOrder($order);
        $order->inventory_settled = true;
        $this->bustInventoryCache();
    }

    public function unsettleForOrder(Order $order): void
    {
        if (! $order->inventory_settled && ! $this->orderHasDeduct((string) $order->id)) {
            $this->rollbackForOrder($order);
            $this->bustInventoryCache();

            return;
        }

        $this->rollbackForOrder($order);
        $order->inventory_settled = false;
        $this->bustInventoryCache();
    }

    public function productGroupedRows(bool $lowStockOnly = false, ?string $search = null): Collection
    {
        return $this->variantRows($lowStockOnly, $search)
            ->groupBy('product_id')
            ->map(function (Collection $variants, string $productId) {
                $first = $variants->first();

                return [
                    'product_id' => $productId,
                    'product_name' => $first['product_name'],
                    'sku' => $first['sku'],
                    'stock' => $variants->sum('stock'),
                    'reserved_stock' => $variants->sum('reserved_stock'),
                    'available_stock' => $variants->sum('available_stock'),
                    'stock_value' => $variants->sum('stock_value'),
                    'has_variants' => $variants->contains(fn (array $row) => $row['is_variant']),
                    'variants' => $variants->values()->all(),
                ];
            })
            ->sortBy('product_name')
            ->values();
    }

    public function cachedVariantRows(): Collection
    {
        return Cache::remember('admin.inventory.variant_rows', 120, fn () => $this->variantRows());
    }

    public function variantRows(bool $lowStockOnly = false, ?string $search = null): Collection
    {
        $query = ProductVariant::query()
            ->with(['product:id,name,purchase_price,sku'])
            ->orderBy('product_id')
            ->orderBy('size')
            ->orderBy('color');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $variants = $query->get()->map(function (ProductVariant $variant) {
            $available = $this->availableUnits($variant);
            $purchasePrice = (float) ($variant->product?->purchase_price ?? 0);

            return [
                'id' => (string) $variant->id,
                'product_id' => (string) $variant->product_id,
                'product_name' => $variant->product?->name ?? '—',
                'variant_label' => $variant->display_name,
                'sku' => $variant->sku,
                'stock' => (int) $variant->stock,
                'reserved_stock' => (int) ($variant->reserved_stock ?? 0),
                'available_stock' => $available,
                'purchase_price' => $purchasePrice,
                'stock_value' => $available * $purchasePrice,
                'is_variant' => true,
            ];
        });

        $simpleProducts = Product::query()
            ->whereDoesntHave('variants')
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                $available = $this->availableUnits($product);
                $purchasePrice = (float) ($product->purchase_price ?? 0);

                return [
                    'id' => (string) $product->id,
                    'product_id' => (string) $product->id,
                    'product_name' => $product->name,
                    'variant_label' => '—',
                    'sku' => $product->sku,
                    'stock' => (int) $product->stock,
                    'reserved_stock' => (int) ($product->reserved_stock ?? 0),
                    'available_stock' => $available,
                    'purchase_price' => $purchasePrice,
                    'stock_value' => $available * $purchasePrice,
                    'is_variant' => false,
                ];
            });

        $rows = $variants->concat($simpleProducts)->sortBy('product_name')->values();

        if ($lowStockOnly) {
            $rows = $rows->filter(fn (array $row) => $row['available_stock'] < $this->lowStockThreshold())->values();
        }

        return $rows;
    }

    public function movementLog(int $limit = 100): Collection
    {
        if ($this->usesMongoMovements()) {
            return StockMovement::query()
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        }

        return collect(DB::table('stock_movements')->orderByDesc('created_at')->limit($limit)->get())
            ->map(fn ($row) => (object) [
                'id' => $row->id,
                'product_variant_id' => $row->product_variant_id,
                'product_id' => $row->product_id,
                'order_id' => $row->order_id,
                'type' => StockMovementType::from($row->type),
                'quantity' => (int) $row->quantity,
                'note' => $row->note,
                'admin_id' => $row->admin_id,
                'created_at' => \Carbon\Carbon::parse($row->created_at),
            ]);
    }

    public function summary(): array
    {
        $rows = $this->cachedVariantRows();

        return [
            'total_stock_value' => round($rows->sum('stock_value'), 2),
            'low_stock_products' => $rows
                ->filter(fn (array $row) => $row['available_stock'] < $this->lowStockThreshold())
                ->take(8)
                ->values()
                ->all(),
            'low_stock_count' => $rows->filter(fn (array $row) => $row['available_stock'] < $this->lowStockThreshold())->count(),
            'threshold' => $this->lowStockThreshold(),
        ];
    }

    private function reserveOrderItem(OrderItem $item, string $orderId): void
    {
        if ($item->product_variant_id) {
            $this->reserve((string) $item->product_variant_id, $orderId, (int) $item->quantity);

            return;
        }

        $this->reserveProduct((string) $item->product_id, $orderId, (int) $item->quantity);
    }

    private function deductOrderItem(OrderItem $item, string $orderId): void
    {
        if ($item->product_variant_id) {
            $this->deduct((string) $item->product_variant_id, $orderId, (int) $item->quantity);

            return;
        }

        $this->deductProduct((string) $item->product_id, $orderId, (int) $item->quantity);
    }

    private function rollbackOrderItem(OrderItem $item, string $orderId): void
    {
        if ($item->product_variant_id) {
            $this->rollback((string) $item->product_variant_id, $orderId, (int) $item->quantity);

            return;
        }

        $this->rollbackProduct((string) $item->product_id, $orderId, (int) $item->quantity);
    }

    private function availableUnits(ProductVariant|Product $model): int
    {
        return max(0, (int) $model->stock - (int) ($model->reserved_stock ?? 0));
    }

    private function recordMovement(array $data): void
    {
        $payload = [
            ...$data,
            'type' => $data['type'] instanceof StockMovementType ? $data['type']->value : $data['type'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($this->usesMongoMovements()) {
            StockMovement::query()->create($payload);

            return;
        }

        DB::table('stock_movements')->insert([
            'product_variant_id' => $payload['product_variant_id'] ?? null,
            'product_id' => isset($payload['product_id']) ? (int) $payload['product_id'] : null,
            'order_id' => $payload['order_id'] ?? null,
            'type' => $payload['type'],
            'quantity' => $payload['quantity'],
            'note' => $payload['note'] ?? null,
            'admin_id' => isset($payload['admin_id']) ? (int) $payload['admin_id'] : null,
            'created_at' => $payload['created_at'],
            'updated_at' => $payload['updated_at'],
        ]);
    }

    private function orderHasDeduct(string $orderId): bool
    {
        if ($this->usesMongoMovements()) {
            return StockMovement::query()
                ->where('order_id', $orderId)
                ->where('type', StockMovementType::Deduct->value)
                ->exists();
        }

        return DB::table('stock_movements')
            ->where('order_id', $orderId)
            ->where('type', StockMovementType::Deduct->value)
            ->exists();
    }

    private function usesMongoMovements(): bool
    {
        return config('database.default') === 'mongodb'
            && class_exists(StockMovement::class)
            && config('database.connections.mongodb.driver') === 'mongodb';
    }

    private function bustInventoryCache(): void
    {
        Cache::forget('admin.dashboard.inventory');
        Cache::forget('admin.inventory.variant_rows');
    }

    private function assertPositiveQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1.');
        }
    }
}
