<?php

namespace App\Providers;

use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\FooterSettingRepositoryInterface;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use App\Repositories\Contracts\HomepageSectionRepositoryInterface;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use App\Repositories\Contracts\CmsPageRepositoryInterface;
use App\Repositories\Contracts\MediaRepositoryInterface;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use App\Repositories\Contracts\ThemeSettingRepositoryInterface;
use App\Repositories\Eloquent\FooterSettingRepository;
use App\Repositories\Eloquent\HeroSlideRepository;
use App\Repositories\Eloquent\HomepageSectionRepository;
use App\Repositories\Eloquent\LicenseRepository;
use App\Repositories\Eloquent\CmsPageRepository;
use App\Repositories\Eloquent\MediaRepository;
use App\Repositories\Eloquent\MenuRepository;
use App\Repositories\Eloquent\PostRepository;
use App\Repositories\Eloquent\NewsletterRepository;
use App\Repositories\Eloquent\ThemeSettingRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\CouponRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ReviewRepository;
use App\Repositories\Eloquent\SettingRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Eloquent\BrandRepository;
use App\Repositories\Eloquent\ExpenseRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        SettingRepositoryInterface::class => SettingRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        BrandRepositoryInterface::class => BrandRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        CouponRepositoryInterface::class => CouponRepository::class,
        ReviewRepositoryInterface::class => ReviewRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
        ThemeSettingRepositoryInterface::class => ThemeSettingRepository::class,
        HomepageSectionRepositoryInterface::class => HomepageSectionRepository::class,
        HeroSlideRepositoryInterface::class => HeroSlideRepository::class,
        FooterSettingRepositoryInterface::class => FooterSettingRepository::class,
        NewsletterRepositoryInterface::class => NewsletterRepository::class,
        LicenseRepositoryInterface::class => LicenseRepository::class,
        CmsPageRepositoryInterface::class => CmsPageRepository::class,
        MenuRepositoryInterface::class => MenuRepository::class,
        MediaRepositoryInterface::class => MediaRepository::class,
        PostRepositoryInterface::class => PostRepository::class,
        ExpenseRepositoryInterface::class => ExpenseRepository::class,
    ];

    public function register(): void
    {
        //
    }
}
