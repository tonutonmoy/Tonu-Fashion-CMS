<?php

namespace App\Providers;

use App\Enums\MenuLocation;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\BuilderPublishService;
use App\Services\CartService;
use App\Services\FooterBuilderService;
use App\Services\MenuBuilderService;
use App\Services\StorefrontCacheService;
use App\Services\ThemeCustomizerService;
use App\Services\InventoryService;
use App\Services\AdminNotificationService;
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
        Order::observe(OrderObserver::class);

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
        if (is_dir($cacheData) && ! is_writable($cacheData)) {
            @chmod($cacheData, 0775);
        }


        $themePatterns = [
            'themes.*',
            'layouts.frontend',
            'layouts.guest',
        ];

        View::composer($themePatterns, function ($view) {
            $cache = app(StorefrontCacheService::class);
            $payload = $cache->rememberForever('storefront.layout', function () use ($cache) {
                $theme = app(ThemeService::class);
                $themeSettings = $cache->rememberForever('storefront.theme_settings', fn () => $theme->settings());
                $footer = $cache->rememberForever('storefront.footer', fn () => app(FooterBuilderService::class)->get());
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

        View::composer(['layouts.admin', 'partials.admin.header'], function ($view) {
            $payload = [
                'storeSettings' => [
                    'name' => setting('name', config('app.name')),
                ],
                'lowStockCount' => 0,
                'lowStockThreshold' => app(InventoryService::class)->lowStockThreshold(),
            ];

            if (auth()->check() && auth()->user()?->canAdmin('store')) {
                $userId = (string) auth()->id();
                $headerKey = "admin.header.notifications.{$userId}";

                $cached = Cache::remember($headerKey, 120, function () {
                    $inventory = app(InventoryService::class)->summary();

                    return [
                        'threshold' => $inventory['threshold'] ?? app(InventoryService::class)->lowStockThreshold(),
                        'unread' => app(AdminNotificationService::class)->unreadLowStockCountFromSummary(
                            $inventory,
                            auth()->user(),
                        ),
                    ];
                });

                $payload['lowStockCount'] = $cached['unread'];
                $payload['lowStockThreshold'] = $cached['threshold'];
            }

            $view->with($payload);
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
