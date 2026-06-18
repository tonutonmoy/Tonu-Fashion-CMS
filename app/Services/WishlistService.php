<?php

namespace App\Services;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    private const SESSION_KEY = 'guest_wishlist';

    public function getItems(): Collection
    {
        if (Auth::check()) {
            return WishlistItem::query()
                ->with(['product.images', 'product.category'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        $ids = $this->guestProductIds();

        if ($ids->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->with(['images', 'category'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return $ids
            ->map(function ($id) use ($products) {
                $product = $products->get($id);
                if (! $product) {
                    return null;
                }

                $item = new WishlistItem([
                    'product_id' => $product->id,
                ]);
                $item->setRelation('product', $product);

                return $item;
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        if (Auth::check()) {
            return WishlistItem::query()->where('user_id', Auth::id())->count();
        }

        return $this->guestProductIds()->count();
    }

    public function toggle(Product $product): bool
    {
        if (Auth::check()) {
            $item = WishlistItem::query()
                ->where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($item) {
                $item->delete();

                return false;
            }

            WishlistItem::query()->create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]);

            return true;
        }

        $ids = $this->guestProductIds();
        $productId = $product->id;

        if ($ids->contains($productId)) {
            $this->storeGuestIds($ids->reject(fn ($id) => (int) $id === $productId)->values());

            return false;
        }

        $this->storeGuestIds($ids->push($productId)->unique()->values());

        return true;
    }

    public function remove(int $productId): void
    {
        if (Auth::check()) {
            WishlistItem::query()
                ->where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();

            return;
        }

        $this->storeGuestIds(
            $this->guestProductIds()->reject(fn ($id) => (int) $id === $productId)->values()
        );
    }

    public function has(Product $product): bool
    {
        if (Auth::check()) {
            return WishlistItem::query()
                ->where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
        }

        return $this->guestProductIds()->contains($product->id);
    }

    private function guestProductIds(): Collection
    {
        return collect(session(self::SESSION_KEY, []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
    }

    private function storeGuestIds(Collection $ids): void
    {
        session([self::SESSION_KEY => $ids->values()->all()]);
    }
}
