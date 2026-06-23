<?php

namespace App\Console\Commands;

use App\Enums\RecordStatus;
use App\Models\Product;
use App\Services\HomepageBuilderService;
use App\Services\ProductPageService;
use App\Services\StorefrontCacheService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class WarmStorefrontCacheCommand extends Command
{
    protected $signature = 'storefront:warm-cache {--views : Also compile Blade views}';

    protected $description = 'Pre-build homepage, layout, shop, theme, marketing, and HTML caches';

    public function handle(HomepageBuilderService $homepage, StorefrontCacheService $cache): int
    {
        @ini_set('memory_limit', '512M');

        $this->info('Warming storefront caches…');
        $cache->forgetAll();
        $ttl = $cache->ttl();

        foreach (['en', 'bn'] as $locale) {
            app()->setLocale($locale);
            $homepage->getPageData();
            $this->line("  homepage.page_data ({$locale})");
            gc_collect_cycles();
        }

        app()->setLocale(config('app.locale', 'en'));

        Cache::rememberForever('storefront.layout', function () {
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

        Cache::remember('shop.catalog_meta', $ttl, fn () => [
            'categories' => app(\App\Repositories\Contracts\CategoryRepositoryInterface::class)->getActiveOrdered(),
            'brands' => app(\App\Repositories\Contracts\BrandRepositoryInterface::class)->getActive(),
            'priceBounds' => app(\App\Repositories\Contracts\ProductRepositoryInterface::class)->getPriceBounds(),
        ]);
        $this->line('  shop.catalog_meta');

        $perPage = config('fashion.pagination.products');
        Cache::remember('shop.products.page1.'.$perPage, $ttl, fn () => app(\App\Repositories\Contracts\ProductRepositoryInterface::class)->paginateShop([], $perPage));
        $this->line('  shop.products.page1');

        Cache::rememberForever('storefront.marketing', fn () => [
            'pixels' => setting('marketing_pixels', []),
            'gtm' => setting('gtm_container_id'),
        ]);
        $this->line('  storefront.marketing');

        $productSlugs = Product::query()
            ->where('status', RecordStatus::Active)
            ->orderByDesc('featured')
            ->latest()
            ->limit(48)
            ->pluck('slug')
            ->all();

        app(ProductPageService::class)->warmSlugs($productSlugs);
        $this->line('  product.pages ('.count($productSlugs).')');

        $this->warmHtmlPages($productSlugs);

        if ($this->option('views')) {
            Artisan::call('view:cache');
            $this->line('  view:cache');
        }

        $this->info('Storefront cache warm complete.');

        return self::SUCCESS;
    }

    private function warmHtmlPages(array $productSlugs): void
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $paths = array_merge(['/', '/shop'], array_map(fn ($slug) => '/products/'.$slug, $productSlugs));

        foreach (['en', 'bn'] as $locale) {
            app()->setLocale($locale);

            foreach (config('performance.color_modes', ['light', 'dark']) as $mode) {
                foreach ($paths as $path) {
                    $this->renderPath($path, $host, $mode);
                }
            }
        }

        app()->setLocale(config('app.locale', 'en'));
        $modeCount = count(config('performance.color_modes', ['light', 'dark']));
        $this->line('  storefront.html ('.count($paths).' pages x 2 locales x '.$modeCount.' modes)');
    }

    private function renderPath(string $path, string $host, string $colorMode = 'light'): void
    {
        $kernel = app(Kernel::class);
        $request = Request::create($path, 'GET');
        $request->cookies->set('color_mode', $colorMode);
        $request->headers->set('Host', $host);
        $request->headers->set('Accept', 'text/html');
        $request->server->set('HTTP_HOST', $host);
        $request->server->set('SERVER_NAME', $host);

        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
    }
}
