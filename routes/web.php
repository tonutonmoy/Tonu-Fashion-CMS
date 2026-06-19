<?php

use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\CourierModuleController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\MarketingModuleController;
use App\Http\Controllers\Admin\PaymentModuleController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\PerformanceController;
use App\Http\Controllers\Admin\OrderParcelController;
use App\Http\Controllers\Admin\SupportChatController as AdminSupportChatController;
use App\Http\Controllers\Admin\TeamUserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\Cms\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\Cms\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\Cms\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\Cms\PageController as AdminPageController;
use App\Http\Controllers\Admin\StorePreferencesController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\Api\LicenseValidationController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\BrandController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Admin\WebsiteBuilderController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ShopController;
use App\Http\Controllers\Frontend\SupportChatController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/install.php';

Route::get('/preferences/locale/{locale}', [PreferenceController::class, 'locale'])->name('preferences.locale');
Route::get('/preferences/color-mode/{mode}', [PreferenceController::class, 'colorMode'])->name('preferences.color-mode');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/pages/{slug}', [FrontendPageController::class, 'show'])->name('pages.show');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home/section/{key}', [HomeController::class, 'section'])->name('home.section');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/search', [ShopController::class, 'search'])->name('shop.search');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{slug}/related', [ProductController::class, 'related'])->name('products.related');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/brands/{brand:slug}', [BrandController::class, 'show'])->name('brands.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => redirect()->route('home'))->name('login');
    Route::get('/register', fn () => redirect()->route('home'))->name('register');
    Route::get('/forgot-password', fn () => redirect()->route('home'))->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
Route::match(['get', 'post'], '/checkout/shipping-quote', [CheckoutController::class, 'shippingQuote'])->name('checkout.shipping-quote');

Route::prefix('api/bd')->name('api.bd.')->group(function () {
    Route::get('/divisions', [\App\Http\Controllers\Api\BangladeshLocationController::class, 'divisions']);
    Route::get('/districts/{division}', [\App\Http\Controllers\Api\BangladeshLocationController::class, 'districts']);
    Route::get('/areas', [\App\Http\Controllers\Api\BangladeshLocationController::class, 'areas']);
    Route::post('/shipping-quote', [\App\Http\Controllers\Api\BangladeshLocationController::class, 'shippingQuote']);
});

Route::prefix('api/cart')->name('api.cart.')->group(function () {
    Route::get('/', [CartApiController::class, 'show'])->name('show');
    Route::post('/', [CartApiController::class, 'store'])->name('store');
    Route::patch('/{id}', [CartApiController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartApiController::class, 'destroy'])->name('destroy');
});

Route::post('/api/license/validate', [LicenseValidationController::class, 'validate'])->name('api.license.validate');

Route::prefix('api/support')->name('api.support.')->group(function () {
    Route::get('/resume', [SupportChatController::class, 'resume'])->name('resume');
    Route::post('/session', [SupportChatController::class, 'session'])->name('session');
    Route::get('/conversations/{conversation}', [SupportChatController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [SupportChatController::class, 'store'])->name('conversations.messages.store');
    Route::post('/conversations/{conversation}/read', [SupportChatController::class, 'read'])->name('conversations.read');
});

Route::match(['get', 'post'], '/payments/callback/{gateway}', [\App\Http\Controllers\Frontend\PaymentCallbackController::class, 'callback'])->name('payments.callback');
Route::post('/payments/ipn/{gateway}', [\App\Http\Controllers\Frontend\PaymentCallbackController::class, 'ipn'])->name('payments.ipn');

Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

Route::redirect('/track-order', '/');
Route::redirect('/orders', '/');
Route::redirect('/profile', '/');

Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/reviews', [ProductController::class, 'storeReview'])->name('products.reviews.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/preferences', [StorePreferencesController::class, 'update'])->name('preferences.update');

        Route::middleware('role:SUPER_ADMIN')->group(function () {
            Route::resource('users', TeamUserController::class)->except(['show']);
        });

        Route::middleware(['role:SUPER_ADMIN,ADMIN,STAFF', 'admin.permission:store'])->group(function () {
            Route::resource('categories', AdminCategoryController::class)->except(['show']);
            Route::resource('brands', AdminBrandController::class)->except(['show']);
            Route::resource('products', AdminProductController::class)->except(['show']);
            Route::resource('coupons', AdminCouponController::class)->except(['show']);

            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
            Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store');
            Route::get('/orders/{orderNumber}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
            Route::patch('/orders/customers/{user}/restrictions', [AdminOrderController::class, 'updateCustomerRestrictions'])->name('orders.customers.restrictions');
            Route::post('/orders/{orderNumber}/parcel', [OrderParcelController::class, 'createParcel'])->name('orders.parcel.create');
            Route::post('/orders/{orderNumber}/parcel/sync', [OrderParcelController::class, 'syncParcel'])->name('orders.parcel.sync');
            Route::get('/orders/{orderNumber}/invoice/{format?}', [OrderParcelController::class, 'invoice'])->name('orders.invoice');
            Route::get('/orders/{orderNumber}/packing-slip', [OrderParcelController::class, 'packingSlip'])->name('orders.packing-slip');
            Route::get('/orders/{orderNumber}/label', [OrderParcelController::class, 'label'])->name('orders.label');

            Route::prefix('courier')->name('courier.')->group(function () {
                Route::get('/', [CourierModuleController::class, 'index'])->name('index');
                Route::get('/activity', [CourierModuleController::class, 'activity'])->name('activity');
            });

            Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
            Route::patch('/customers/{id}/restrictions', [AdminCustomerController::class, 'updateRestrictions'])->name('customers.restrictions');

            Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
            Route::patch('/reviews/{id}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
            Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

            Route::get('/support', [AdminSupportChatController::class, 'index'])->name('support.index');
            Route::get('/support/{conversation}', [AdminSupportChatController::class, 'show'])->name('support.show');
            Route::post('/support/{conversation}/messages', [AdminSupportChatController::class, 'store'])->name('support.messages.store');
            Route::get('/support/{conversation}/poll', [AdminSupportChatController::class, 'poll'])->name('support.poll');
            Route::patch('/support/{conversation}/close', [AdminSupportChatController::class, 'close'])->name('support.close');
            Route::get('/api/support/notifications', [AdminSupportChatController::class, 'notifications'])->name('support.notifications');
        });

        Route::middleware('role:SUPER_ADMIN,ADMIN,STAFF')->group(function () {
            Route::prefix('cms')->name('cms.')->middleware('admin.permission:blog')->group(function () {
                Route::resource('blog', AdminBlogController::class)->except(['show']);
            });

            Route::prefix('cms')->name('cms.')->middleware('admin.permission:cms')->group(function () {
                Route::resource('pages', AdminPageController::class)->except(['show']);
                Route::get('/menus', [AdminMenuController::class, 'index'])->name('menus.index');
                Route::get('/menus/{location}', [AdminMenuController::class, 'edit'])->name('menus.edit');
                Route::put('/menus/{location}', [AdminMenuController::class, 'update'])->name('menus.update');
                Route::get('/media', [AdminMediaController::class, 'index'])->name('media.index');
                Route::post('/media', [AdminMediaController::class, 'store'])->name('media.store');
                Route::delete('/media/{id}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
                Route::get('/media/search', [AdminMediaController::class, 'search'])->name('media.search');
            });
        });

        Route::middleware(['role:SUPER_ADMIN,ADMIN,STAFF', 'admin.permission:settings'])->group(function () {
            Route::prefix('courier')->name('courier.')->group(function () {
                Route::put('/', [CourierModuleController::class, 'update']);
            });

            Route::get('/settings/store', [SettingController::class, 'storeSettings'])->name('settings.store');
            Route::put('/settings/store', [SettingController::class, 'updateStoreSettings']);
            Route::redirect('/settings/marketing', '/admin/marketing')->name('settings.marketing');

            Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
            Route::post('/performance/warm-cache', [PerformanceController::class, 'warmCache'])->name('performance.warm-cache');

            Route::prefix('backup')->name('backup.')->group(function () {
                Route::get('/', [BackupController::class, 'index'])->name('index');
                Route::post('/', [BackupController::class, 'store'])->name('store');
                Route::get('/{filename}/download', [BackupController::class, 'download'])->name('download');
                Route::post('/{filename}/restore', [BackupController::class, 'restore'])->name('restore');
                Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('payment')->name('payment.')->group(function () {
                Route::get('/', [PaymentModuleController::class, 'index'])->name('index');
                Route::put('/', [PaymentModuleController::class, 'update']);
            });

            Route::middleware('role:SUPER_ADMIN,ADMIN')->group(function () {
                Route::prefix('license')->name('license.')->group(function () {
                    Route::get('/', [LicenseController::class, 'index'])->name('index');
                    Route::get('/{license}/edit', [LicenseController::class, 'edit'])->name('edit');
                    Route::put('/{license}', [LicenseController::class, 'update'])->name('update');
                    Route::patch('/{license}/assign-domain', [LicenseController::class, 'assignDomain'])->name('assign-domain');
                    Route::patch('/{license}/suspend', [LicenseController::class, 'suspend'])->name('suspend');
                    Route::patch('/{license}/expire', [LicenseController::class, 'expire'])->name('expire');
                    Route::patch('/{license}/activate', [LicenseController::class, 'activate'])->name('activate');
                    Route::delete('/{license}', [LicenseController::class, 'destroy'])->name('destroy');
                });
            });

            Route::prefix('marketing')->name('marketing.')->group(function () {
                Route::get('/', [MarketingModuleController::class, 'marketing'])->name('index');
                Route::put('/', [MarketingModuleController::class, 'updateMarketing']);
                Route::get('/shipping', [MarketingModuleController::class, 'shipping'])->name('shipping');
                Route::put('/shipping', [MarketingModuleController::class, 'updateShipping']);
                Route::get('/sms', [MarketingModuleController::class, 'sms'])->name('sms');
                Route::put('/sms', [MarketingModuleController::class, 'updateSms']);
                Route::get('/sms/balance', [MarketingModuleController::class, 'smsBalance'])->name('sms.balance');
                Route::post('/sms/test', [MarketingModuleController::class, 'testSms'])->name('sms.test');
                Route::get('/social-chat', [MarketingModuleController::class, 'socialChat'])->name('social-chat');
                Route::put('/social-chat', [MarketingModuleController::class, 'updateSocialChat']);
                Route::get('/seo', [MarketingModuleController::class, 'seo'])->name('seo');
                Route::put('/seo', [MarketingModuleController::class, 'updateSeo']);
            });

            Route::get('/builder', [WebsiteBuilderController::class, 'index'])->name('builder.index');
            Route::post('/builder/publish', [WebsiteBuilderController::class, 'publish'])->name('builder.publish');
            Route::redirect('/theme', '/admin/builder');
            Route::redirect('/cms', '/admin/builder');

            Route::prefix('theme')->name('theme.')->group(function () {
                Route::get('/customizer', [ThemeController::class, 'customizer'])->name('customizer');
                Route::match(['put', 'post'], '/customizer', [ThemeController::class, 'updateCustomizer']);
                Route::match(['post', 'put'], '/customizer/reset', [ThemeController::class, 'resetCustomizer'])->name('customizer.reset');
                Route::get('/seo', [ThemeController::class, 'seo'])->name('seo');
                Route::put('/seo', [ThemeController::class, 'updateSeo']);
                Route::get('/homepage', [ThemeController::class, 'homepage'])->name('homepage');
                Route::patch('/homepage/reorder', [ThemeController::class, 'reorderHomepage'])->name('homepage.reorder');
                Route::put('/homepage/{id}', [ThemeController::class, 'updateHomepageSection'])->name('homepage.update');
                Route::patch('/homepage/{id}/toggle', [ThemeController::class, 'toggleSection'])->name('homepage.toggle');
                Route::get('/hero-slides', [ThemeController::class, 'heroSlides'])->name('hero-slides');
                Route::put('/hero-slides', [ThemeController::class, 'updateHero'])->name('hero-slides.update');
                Route::get('/footer', [ThemeController::class, 'footer'])->name('footer');
                Route::put('/footer', [ThemeController::class, 'updateFooter']);
            });
        });
    });
});
