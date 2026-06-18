<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const COUNT_CACHE_KEY = 'storefront.cart_count';

    private const TOUCHED_KEY = 'storefront.cart_touched';

    public function getItems(): Collection
    {
        return CartItem::query()
            ->with(['product.images', 'variant'])
            ->when(Auth::check(), fn ($q) => $q->where('user_id', Auth::id()))
            ->when(! Auth::check(), fn ($q) => $q->where('session_id', Session::getId()))
            ->get();
    }

    public function count(): int
    {
        if (! Auth::check() && ! Session::get(self::TOUCHED_KEY, false)) {
            return 0;
        }

        if (Session::has(self::COUNT_CACHE_KEY)) {
            return (int) Session::get(self::COUNT_CACHE_KEY);
        }

        $count = (int) $this->getItems()->sum('quantity');
        Session::put(self::COUNT_CACHE_KEY, $count);

        return $count;
    }

    public function subtotal(): float
    {
        return $this->getItems()->sum(fn (CartItem $item) => $item->line_total);
    }

    public function add(Product $product, int $quantity = 1, ?ProductVariant $variant = null): CartItem
    {
        $attributes = [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
        ];

        if (Auth::check()) {
            $attributes['user_id'] = Auth::id();
        } else {
            $attributes['session_id'] = Session::getId();
        }

        $item = CartItem::query()->firstOrNew($attributes);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->save();
        $this->forgetCountCache();

        return $item->load(['product.images', 'variant']);
    }

    public function update(int $cartItemId, int $quantity): void
    {
        $item = $this->findOwnedItem($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
            $this->forgetCountCache();

            return;
        }

        $item->update(['quantity' => $quantity]);
        $this->forgetCountCache();
    }

    public function remove(int $cartItemId): void
    {
        $this->findOwnedItem($cartItemId)->delete();
        $this->forgetCountCache();
    }

    public function clear(): void
    {
        CartItem::query()
            ->when(Auth::check(), fn ($q) => $q->where('user_id', Auth::id()))
            ->when(! Auth::check(), fn ($q) => $q->where('session_id', Session::getId()))
            ->delete();
        $this->forgetCountCache();
    }

    public function mergeGuestCart(int $userId): void
    {
        $sessionId = Session::getId();

        CartItem::query()
            ->where('session_id', $sessionId)
            ->whereNull('user_id')
            ->each(function (CartItem $guestItem) use ($userId) {
                $existing = CartItem::query()
                    ->where('user_id', $userId)
                    ->where('product_id', $guestItem->product_id)
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existing) {
                    $existing->increment('quantity', $guestItem->quantity);
                    $guestItem->delete();
                } else {
                    $guestItem->update(['user_id' => $userId, 'session_id' => null]);
                }
            });
        $this->forgetCountCache();
    }

    private function forgetCountCache(): void
    {
        Session::put(self::TOUCHED_KEY, true);
        Session::forget(self::COUNT_CACHE_KEY);
    }

    private function findOwnedItem(int $id): CartItem
    {
        return CartItem::query()
            ->when(Auth::check(), fn ($q) => $q->where('user_id', Auth::id()))
            ->when(! Auth::check(), fn ($q) => $q->where('session_id', Session::getId()))
            ->findOrFail($id);
    }
}
