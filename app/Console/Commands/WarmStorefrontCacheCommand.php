<?php

namespace App\Console\Commands;

use App\Services\HomepageBuilderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmStorefrontCacheCommand extends Command
{
    protected $signature = 'storefront:warm-cache';

    protected $description = 'Pre-build homepage, layout, and shop caches for fast first page load';

    public function handle(HomepageBuilderService $homepage): int
    {
        $this->info('Warming storefront caches…');

        $homepage->getPageData();
        $this->line('  homepage.page_data');

        Cache::remember('storefront.layout', 600, function () {
            $theme = app(\App\Services\ThemeService::class);
            $themeSettings = $theme->settings();
            $footer = app(\App\Services\FooterBuilderService::class)->get();
            $menus = app(\App\Services\MenuBuilderService::class);

            return [
                'themeSettings' => $themeSettings,
                'footerSettings' => $footer,
                'activeTheme' => $theme->activeSlug(),
                'headerMenu' => filter_storefront_menu($menus->getTree(\App\Enums\MenuLocation::Header)),
                'footerMenu' => filter_storefront_menu($menus->getTree(\App\Enums\MenuLocation::Footer)),
                'storeSettings' => [
                    'name' => setting('name', config('app.name')),
                    'logo' => $themeSettings->logo ?? setting('logo'),
                    'favicon' => $themeSettings->favicon ?? setting('favicon'),
                    'phone' => $footer->phone ?? setting('phone'),
                    'email' => $footer->email ?? setting('email'),
                    'address' => $footer->address ?? setting('address'),
                    'facebook' => $footer->facebook_url ?? setting('facebook_url'),
                    'instagram' => $footer->instagram_url ?? setting('instagram_url'),
                    'whatsapp' => $footer->whatsapp_number ?? setting('whatsapp_number'),
                    'messenger' => $footer->messenger_link ?? setting('messenger_link'),
                ],
            ];
        });
        $this->line('  storefront.layout');

        Cache::remember('shop.catalog_meta', 600, fn () => [
            'categories' => app(\App\Repositories\Contracts\CategoryRepositoryInterface::class)->getActiveOrdered(),
            'brands' => app(\App\Repositories\Contracts\BrandRepositoryInterface::class)->getActive(),
            'priceBounds' => app(\App\Repositories\Contracts\ProductRepositoryInterface::class)->getPriceBounds(),
        ]);
        $this->line('  shop.catalog_meta');

        $perPage = config('fashion.pagination.products');
        Cache::remember('shop.products.page1.'.$perPage, 300, fn () => app(\App\Repositories\Contracts\ProductRepositoryInterface::class)->paginateShop([], $perPage));
        $this->line('  shop.products.page1');

        $this->info('Storefront cache warm complete.');

        return self::SUCCESS;
    }
}
