<?php

namespace App\Providers;

use App\Enums\MenuLocation;
use App\Services\BuilderPublishService;
use App\Services\CartService;
use App\Services\FooterBuilderService;
use App\Services\MenuBuilderService;
use App\Services\StorefrontCacheService;
use App\Services\ThemeCustomizerService;
use App\Services\ThemeService;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production') && str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        if (config('performance.profiling')) {
            Event::listen(CacheHit::class, function () {
                app(StorefrontCacheService::class)->recordCacheHit();
            });

            Event::listen(CacheMissed::class, function () {
                app(StorefrontCacheService::class)->recordCacheMiss();
            });
        }

        $cacheData = storage_path('framework/cache/data');
        if (! is_dir($cacheData)) {
            @mkdir($cacheData, 0775, true);
        }

        $ttl = app(StorefrontCacheService::class)->ttl();

        $themePatterns = [
            'themes.*',
            'layouts.frontend',
            'layouts.guest',
        ];

        View::composer($themePatterns, function ($view) use ($ttl) {
            $payload = Cache::remember('storefront.layout', $ttl, function () {
                $theme = app(ThemeService::class);
                $themeSettings = Cache::remember('storefront.theme_settings', app(StorefrontCacheService::class)->ttl(), fn () => $theme->settings());
                $footer = Cache::remember('storefront.footer', app(StorefrontCacheService::class)->ttl(), fn () => app(FooterBuilderService::class)->get());
                $menus = app(MenuBuilderService::class);

                return [
                    'themeSettings' => $themeSettings,
                    'footerSettings' => $footer,
                    'activeTheme' => $theme->activeSlug(),
                    'headerMenu' => filter_storefront_menu($menus->getTree(MenuLocation::Header)),
                    'footerMenu' => filter_storefront_menu($menus->getTree(MenuLocation::Footer)),
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

            $view->with([
                ...$payload,
                'cartCount' => app(CartService::class)->count(),
            ]);
        });

        View::composer(['layouts.admin'], function ($view) {
            $view->with('storeSettings', [
                'name' => setting('name', config('app.name')),
            ]);
        });

        View::composer([
            'admin.builder.*',
            'admin.theme.*',
            'components.admin.builder-layout',
        ], function ($view) {
            $view->with('hasUnpublishedChanges', app(BuilderPublishService::class)->hasUnpublishedChanges());
        });
    }
}
