<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class StorefrontCacheService
{
    public function ttl(): int
    {
        return (int) config('performance.cache_ttl', 3600);
    }

    public function localeKey(string $prefix): string
    {
        return $prefix.'.'.app()->getLocale();
    }

    public function forgetAll(): void
    {
        $this->forgetHomepage();
        $this->forgetLayout();
        $this->forgetShop();
    }

    public function forgetHomepage(): void
    {
        foreach (['en', 'bn'] as $locale) {
            Cache::forget("homepage.initial.{$locale}");
            Cache::forget("homepage.page_data.{$locale}");

            foreach (config('performance.homepage_lazy_sections', []) as $section) {
                Cache::forget("homepage.section.{$section}.{$locale}");
            }

            foreach (['featured_products', 'new_arrivals', 'best_sellers', 'flash_sale'] as $pool) {
                Cache::forget("homepage.products.{$pool}.{$locale}");
            }
        }

        Cache::forget('storefront.hero');
        Cache::forget('storefront.homepage_sections');
        Cache::forget('storefront.marketing');
    }

    public function forgetLayout(): void
    {
        Cache::forget('storefront.layout');
        Cache::forget('storefront.theme_settings');
        Cache::forget('storefront.footer');
    }

    public function forgetShop(): void
    {
        Cache::forget('shop.catalog_meta');
        Cache::forget('shop.price_bounds');

        foreach ([12, 24, 36, 48] as $perPage) {
            Cache::forget("shop.products.page1.{$perPage}");
        }
    }

    public function forgetProduct(?string $slug): void
    {
        if (! $slug) {
            return;
        }

        Cache::forget("product.show.{$slug}");
        Cache::forget("product.related.{$slug}");
    }

    public function recordCacheHit(): void
    {
        if (! config('performance.profiling')) {
            return;
        }

        $stats = Cache::get('performance.cache_stats', ['hits' => 0, 'misses' => 0]);
        $stats['hits'] = ($stats['hits'] ?? 0) + 1;
        Cache::put('performance.cache_stats', $stats, 86400);
    }

    public function recordCacheMiss(): void
    {
        if (! config('performance.profiling')) {
            return;
        }

        $stats = Cache::get('performance.cache_stats', ['hits' => 0, 'misses' => 0]);
        $stats['misses'] = ($stats['misses'] ?? 0) + 1;
        Cache::put('performance.cache_stats', $stats, 86400);
    }

    public function cacheStats(): array
    {
        $stats = Cache::get('performance.cache_stats', ['hits' => 0, 'misses' => 0]);
        $hits = (int) ($stats['hits'] ?? 0);
        $misses = (int) ($stats['misses'] ?? 0);
        $total = $hits + $misses;

        return [
            'hits' => $hits,
            'misses' => $misses,
            'hit_rate' => $total > 0 ? round(($hits / $total) * 100, 1) : 0.0,
        ];
    }
}
