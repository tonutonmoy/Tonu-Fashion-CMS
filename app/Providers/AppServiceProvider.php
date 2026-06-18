<?php

namespace App\Providers;

use App\Enums\MenuLocation;
use App\Services\BuilderPublishService;
use App\Services\CartService;
use App\Services\FooterBuilderService;
use App\Services\MenuBuilderService;
use App\Services\ThemeCustomizerService;
use App\Services\ThemeService;
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
        $themePatterns = [
            'themes.*',
            'layouts.frontend',
            'layouts.guest',
        ];

        View::composer($themePatterns, function ($view) {
            $theme = app(ThemeService::class);
            $themeSettings = $theme->settings();
            $footer = app(FooterBuilderService::class)->get();
            $menus = app(MenuBuilderService::class);

            $view->with([
                'themeSettings' => $themeSettings,
                'footerSettings' => $footer,
                'activeTheme' => $theme->activeSlug(),
                'headerMenu' => filter_storefront_menu($menus->getTree(MenuLocation::Header)),
                'footerMenu' => filter_storefront_menu($menus->getTree(MenuLocation::Footer)),
                'cartCount' => app(CartService::class)->count(),
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
