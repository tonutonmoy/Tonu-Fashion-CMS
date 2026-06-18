<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $categoryNames = ['Men', 'Women', 'Kids', 'Accessories'];
        $categoryIds = [];

        foreach ($categoryNames as $index => $name) {
            $category = Category::query()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'status' => RecordStatus::Active,
                'sort_order' => $index + 1,
                'meta_title' => "{$name} | Fashion BD",
                'meta_description' => "Shop {$name} fashion at Fashion BD.",
            ]);
            $categoryIds[] = $category->id;
        }

        $brandIds = [];
        foreach (['Urban Style', 'Classic Wear', 'Trend Line', 'Dhaka Couture', 'Street BD'] as $brandName) {
            $brandIds[] = Brand::query()->create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'status' => RecordStatus::Active,
            ])->id;
        }

        $colors = ['Black', 'White', 'Navy', 'Maroon'];
        $sizes = config('fashion.sizes', ['S', 'M', 'L', 'XL']);

        for ($i = 1; $i <= 48; $i++) {
            $name = "Demo Product {$i}";
            $regular = 800 + ($i * 75);
            $featured = $i <= 12;

            $product = Product::query()->create([
                'name' => $name,
                'slug' => Str::slug($name).'-'.$i,
                'sku' => 'DEMO-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'short_description' => 'Premium quality fashion item for everyday wear.',
                'description' => 'Demo product seeded for Fashion BD. Replace with your own catalog from the admin panel.',
                'regular_price' => $regular,
                'sale_price' => $i % 4 === 0 ? $regular - 200 : null,
                'stock' => 25,
                'featured' => $featured,
                'category_id' => $categoryIds[($i - 1) % count($categoryIds)],
                'brand_id' => $brandIds[($i - 1) % count($brandIds)],
                'status' => RecordStatus::Active,
                'avg_rating' => 4.5,
                'review_count' => 12,
            ]);

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => 'https://picsum.photos/seed/fashion-bd-'.$i.'/800/1000',
                'is_primary' => true,
                'sort_order' => 0,
            ]);

            foreach ($sizes as $size) {
                ProductVariant::query()->create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'color' => $colors[($i + strlen($size)) % count($colors)],
                    'sku' => $product->sku.'-'.$size,
                    'stock' => 10,
                    'status' => RecordStatus::Active,
                ]);
            }
        }
    }
}
