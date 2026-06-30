<?php

namespace Database\Seeders;

use App\Enums\HomepageSectionKey;
use App\Enums\RecordStatus;
use App\Models\Brand;
use App\Models\BuilderDraft;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\StorefrontCacheService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SlimFashionSeeder extends Seeder
{
    /** @var array<int, string> */
    private const HERO_IMAGES = [
        'https://images.unsplash.com/photo-1483985988355-763728e3685b?w=1400&q=80&auto=format',
        'https://images.unsplash.com/photo-1617137968427-85924c800a22?w=1400&q=80&auto=format',
        'https://images.unsplash.com/photo-1445205170230-053b83016050?w=1400&q=80&auto=format',
    ];

    /** @var array<string, string> */
    private const CATEGORY_IMAGES = [
        'Women' => 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=900&q=80&auto=format',
        'Men' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?w=900&q=80&auto=format',
        'Kids' => 'https://images.unsplash.com/photo-1503454537844-cea843a3c5d4?w=900&q=80&auto=format',
        'Accessories' => 'https://images.unsplash.com/photo-1523206489230-c012c64b2b48?w=900&q=80&auto=format',
    ];

    /** @var array<int, string> */
    private const PRODUCT_IMAGES = [
        'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=1200&q=80&auto=format',
        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=1200&q=80&auto=format',
        'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=1200&q=80&auto=format',
        'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=1200&q=80&auto=format',
        'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=1200&q=80&auto=format',
        'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=1200&q=80&auto=format',
        'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=1200&q=80&auto=format',
        'https://images.unsplash.com/photo-1622445275463-79ba06d3411d?w=1200&q=80&auto=format',
    ];

    public function run(): void
    {
        $this->purgeCatalog();

        $categoryIds = [];
        foreach (self::CATEGORY_IMAGES as $name => $image) {
            $category = Category::query()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'image' => $image,
                'status' => RecordStatus::Active,
                'sort_order' => count($categoryIds) + 1,
                'meta_title' => "{$name} Fashion",
                'meta_description' => "Shop {$name} fashion collection.",
            ]);
            $categoryIds[] = $category->id;
        }

        $brandIds = [];
        foreach (['Urban Style', 'Classic Wear', 'Trend Line', 'Dhaka Couture'] as $brandName) {
            $brandIds[] = Brand::query()->create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'status' => RecordStatus::Active,
            ])->id;
        }

        $colors = ['Black', 'White', 'Navy', 'Maroon'];
        $sizes = ['S', 'M', 'L', 'XL'];

        for ($i = 1; $i <= 20; $i++) {
            $name = match (true) {
                $i <= 5 => "Women's Fashion Item {$i}",
                $i <= 10 => "Men's Fashion Item ".($i - 5),
                $i <= 15 => "Kids Wear ".($i - 10),
                default => 'Accessory '.$i,
            };

            $regular = 1200 + ($i * 85);
            $sale = $i % 5 === 0 ? $regular - 250 : null;

            $product = Product::query()->create([
                'name' => $name,
                'slug' => Str::slug($name).'-'.$i,
                'sku' => 'FSH-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'short_description' => 'Premium Bangladesh fashion — comfortable everyday style.',
                'description' => 'Curated fashion piece for your storefront demo catalog.',
                'regular_price' => $regular,
                'sale_price' => $sale,
                'effective_price' => $sale ?? $regular,
                'purchase_price' => max(400, $regular - 350),
                'stock' => 30,
                'featured' => $i <= 8,
                'category_id' => $categoryIds[($i - 1) % count($categoryIds)],
                'brand_id' => $brandIds[($i - 1) % count($brandIds)],
                'status' => RecordStatus::Active,
                'avg_rating' => 4.6,
                'review_count' => 8,
            ]);

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => self::PRODUCT_IMAGES[($i - 1) % count(self::PRODUCT_IMAGES)],
                'is_primary' => true,
                'sort_order' => 0,
            ]);

            foreach ($sizes as $size) {
                ProductVariant::query()->create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'color' => $colors[($i + strlen($size)) % count($colors)],
                    'sku' => $product->sku.'-'.$size,
                    'stock' => 12,
                    'price_adjustment' => 0,
                    'status' => RecordStatus::Active,
                ]);
            }
        }

        $this->seedHeroMedia();
        BuilderDraft::query()->delete();
        HeroSlide::query()->delete();

        app(StorefrontCacheService::class)->forgetAll();
    }

    private function purgeCatalog(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'order_items',
            'orders',
            'wishlist_items',
            'reviews',
            'stock_movements',
            'product_variants',
            'product_images',
            'products',
            'categories',
            'brands',
        ] as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedHeroMedia(): void
    {
        $hero = HomepageSection::query()
            ->where('section_key', HomepageSectionKey::HeroSlider->value)
            ->first();

        if (! $hero) {
            return;
        }

        $media = collect(self::HERO_IMAGES)->values()->map(fn (string $url, int $index) => [
            'id' => 'hero-'.($index + 1),
            'type' => 'image',
            'desktop_image' => $url,
            'mobile_image' => null,
            'sort_order' => $index + 1,
        ])->all();

        $hero->update([
            'enabled' => true,
            'settings' => array_merge($hero->settings ?? [], [
                'title' => 'New Season Fashion',
                'subtitle' => 'Premium styles with cash on delivery across Bangladesh.',
                'button_text' => 'Shop Now',
                'button_link' => '/shop',
                'show_title' => true,
                'show_subtitle' => true,
                'show_button' => true,
                'media' => $media,
            ]),
        ]);
    }
}
