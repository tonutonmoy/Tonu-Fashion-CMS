<?php

namespace App\Enums;

enum HomepageSectionKey: string
{
    case HeroSlider = 'hero_slider';
    case Categories = 'categories';
    case FeaturedProducts = 'featured_products';
    case NewArrivals = 'new_arrivals';
    case FlashSale = 'flash_sale';
    case BestSellers = 'best_sellers';
    case CustomerReviews = 'customer_reviews';
    case Blog = 'blog';
    case Faq = 'faq';
    case Newsletter = 'newsletter';

    public function label(): string
    {
        return match ($this) {
            self::HeroSlider => 'Hero Slider',
            self::Categories => 'Categories',
            self::FeaturedProducts => 'Featured Products',
            self::NewArrivals => 'New Arrivals',
            self::FlashSale => 'Flash Sale',
            self::BestSellers => 'Best Sellers',
            self::CustomerReviews => 'Customer Reviews',
            self::Blog => 'Blog',
            self::Faq => 'FAQ',
            self::Newsletter => 'Newsletter',
        };
    }

    public function defaultSortOrder(): int
    {
        return match ($this) {
            self::HeroSlider => 1,
            self::Categories => 2,
            self::FeaturedProducts => 3,
            self::NewArrivals => 4,
            self::FlashSale => 5,
            self::BestSellers => 6,
            self::CustomerReviews => 7,
            self::Blog => 8,
            self::Faq => 9,
            self::Newsletter => 10,
        };
    }
}
