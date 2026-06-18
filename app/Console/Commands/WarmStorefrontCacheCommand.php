<?php

namespace App\Console\Commands;

use App\Enums\RecordStatus;
use App\Models\Product;
use App\Services\HomepageBuilderService;
use App\Services\ProductPageService;
use App\Services\StorefrontCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class WarmStorefrontCacheCommand extends Command
{
    protected $signature = 'storefront:warm-cache {--views : Also compile Blade views}';

    protected $description = 'Pre-build homepage, layout, shop, theme, and marketing caches';

    public function handle(HomepageBuilderService $homepage, StorefrontCacheService $cache): int
    {
        @ini_set('memory_limit', '512M');

        $this->info('Warming storefront caches…');
        $ttl = $cache->ttl();

        foreach (['en', 'bn'] as $locale) {
            app()->setLocale($locale);

            $homepage->getInitialPageData();
            $this->line("  homepage.initial ({$locale})");

            foreach ($homepage->getLazySectionKeys() as $sectionKey) {
                $homepage->getSectionData($sectionKey);
                $this->line("  homepage.section.{$sectionKey} ({$locale})");
            }

            gc_collect_cycles();
        }

        app()->setLocale(config('app.locale', 'en'));

        Cache::remember('storefront.layout', $ttl, function () {
            $theme = app(\App\Services\ThemeService::class);

            return [
                'themeSettings' => $theme->settings(),
                'footerSettings' => app(\App\Services\FooterBuilderService::class)->get(),
                'activeTheme' => $theme->activeSlug(),
                'headerMenu' => filter_storefront_menu(app(\App\Services\MenuBuilderService::class)->getTree(\App\Enums\MenuLocation::Header)),
                'footerMenu' => filter_storefront_menu(app(\App\Services\MenuBuilderService::class)->getTree(\App\Enums\MenuLocation::Footer)),
                'storeSettings' => ['name' => setting('name', config('app.name'))],
            ];
        });
        $this->line('  storefront.layout');

        Cache::remember('shop.catalog_meta', $ttl, fn () => [
            'categories' => app(\App\Repositories\Contracts\CategoryRepositoryInterface::class)->getActiveOrdered(),
            'brands' => app(\App\Repositories\Contracts\BrandRepositoryInterface::class)->getActive(),
            'priceBounds' => app(\App\Repositories\Contracts\ProductRepositoryInterface::class)->getPriceBounds(),
        ]);
        $this->line('  shop.catalog_meta');

        $perPage = config('fashion.pagination.products');
        Cache::remember('shop.products.page1.'.$perPage, $ttl, fn () => app(\App\Repositories\Contracts\ProductRepositoryInterface::class)->paginateShop([], $perPage));
        $this->line('  shop.products.page1');

        Cache::remember('storefront.marketing', $ttl, fn () => [
            'pixels' => setting('marketing_pixels', []),
            'gtm' => setting('gtm_container_id'),
        ]);
        $this->line('  storefront.marketing');

        $productSlugs = Product::query()
            ->where('status', RecordStatus::Active)
            ->orderByDesc('featured')
            ->latest()
            ->limit(12)
            ->pluck('slug')
            ->all();

        app(ProductPageService::class)->warmSlugs($productSlugs);
        $this->line('  product.pages ('.count($productSlugs).')');

        if ($this->option('views')) {
            Artisan::call('view:cache');
            $this->line('  view:cache');
        }

        $this->info('Storefront cache warm complete.');

        return self::SUCCESS;
    }
}
