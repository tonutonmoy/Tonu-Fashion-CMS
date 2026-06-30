<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class StorefrontCacheService
{
    public function ttl(): int
    {
        return (int) config('performance.cache_ttl', 3600);
    }

    public function rememberForever(string $key, callable $callback): mixed
    {
        return Cache::rememberForever($key, $callback);
    }

    public function remember(string $key, callable $callback): mixed
    {
        return Cache::remember($key, $this->ttl(), $callback);
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
        $this->forgetHtmlCache();
        Cache::forget('admin.inventory.variant_rows');
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
        $this->forgetHtmlCache('home');
    }

    public function forgetLayout(): void
    {
        Cache::forget('storefront.layout');
        Cache::forget('storefront.theme_settings');
        Cache::forget('storefront.footer');
        Cache::forget('theme_settings');
        Cache::forget('footer_settings');

        foreach (['header', 'footer'] as $location) {
            Cache::forget("storefront.menu.{$location}");
        }
    }

    public function forgetShop(): void
    {
        Cache::forget('shop.catalog_meta');
        Cache::forget('shop.price_bounds');

        foreach ([10, 12, 20, 24, 36, 48] as $perPage) {
            Cache::forget("shop.products.page1.{$perPage}");
        }

        $this->forgetHtmlCache('shop.index');
    }

    public function forgetProduct(?string $slug): void
    {
        if (! $slug) {
            return;
        }

        Cache::forget("product.show.{$slug}");
        Cache::forget("product.related.{$slug}");
        $this->forgetHtmlCache('products.show', $slug);
    }

    public function forgetHtmlCache(?string $route = null, ?string $slug = null): void
    {
        $routes = $route ? [$route] : ['home', 'shop.index', 'products.show'];

        foreach (['en', 'bn'] as $locale) {
            foreach ($this->colorModes() as $mode) {
                foreach ($routes as $name) {
                    if ($name === 'products.show') {
                        if ($slug) {
                            Cache::forget($this->htmlCacheKey($name, $locale, $slug, $mode));
                        }

                        continue;
                    }

                    Cache::forget($this->htmlCacheKey($name, $locale, '', $mode));
                }
            }
        }
    }

    public function htmlCacheKey(string $route, string $locale, string $slug = '', string $colorMode = 'light'): string
    {
        return 'storefront.html.'.$route.'.'.$locale.'.'.$colorMode.'.'.md5($slug);
    }

    public function recordCacheHit(): void
    {
        $stats = Cache::get('performance.cache_stats', ['hits' => 0, 'misses' => 0]);
        $stats['hits'] = ($stats['hits'] ?? 0) + 1;
        Cache::put('performance.cache_stats', $stats, 86400);
    }

    public function recordCacheMiss(): void
    {
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

  /** @return list<string> */
    private function colorModes(): array
    {
        return config('performance.color_modes', app(ColorModeService::class)->supported());
    }
}
