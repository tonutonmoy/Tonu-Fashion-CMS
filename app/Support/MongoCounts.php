<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MongoCounts
{
    public static function productsForCategories(LengthAwarePaginator|Collection $categories): void
    {
        $ids = $categories instanceof LengthAwarePaginator
            ? $categories->getCollection()->pluck('id')->all()
            : $categories->pluck('id')->all();

        if ($ids === []) {
            return;
        }

        $counts = Product::query()
            ->whereIn('category_id', $ids)
            ->get(['category_id'])
            ->groupBy('category_id')
            ->map->count();

        $items = $categories instanceof LengthAwarePaginator ? $categories->getCollection() : $categories;

        foreach ($items as $category) {
            $category->setAttribute('products_count', (int) ($counts->get($category->id, 0)));
        }
    }

    public static function productsForBrands(LengthAwarePaginator|Collection $brands): void
    {
        $ids = $brands instanceof LengthAwarePaginator
            ? $brands->getCollection()->pluck('id')->all()
            : $brands->pluck('id')->all();

        if ($ids === []) {
            return;
        }

        $counts = Product::query()
            ->whereIn('brand_id', $ids)
            ->get(['brand_id'])
            ->groupBy('brand_id')
            ->map->count();

        $items = $brands instanceof LengthAwarePaginator ? $brands->getCollection() : $brands;

        foreach ($items as $brand) {
            $brand->setAttribute('products_count', (int) ($counts->get($brand->id, 0)));
        }
    }

    public static function ordersForUsers(LengthAwarePaginator $users): void
    {
        $ids = $users->getCollection()->pluck('id')->all();

        if ($ids === []) {
            return;
        }

        $counts = Order::query()
            ->whereIn('user_id', $ids)
            ->get(['user_id'])
            ->groupBy('user_id')
            ->map->count();

        foreach ($users->getCollection() as $user) {
            $user->setAttribute('orders_count', (int) ($counts->get($user->id, 0)));
        }
    }
}
