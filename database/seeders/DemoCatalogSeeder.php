<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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

        $categories = [
            ['name' => 'Men', 'sort_order' => 1],
            ['name' => 'Women', 'sort_order' => 2],
            ['name' => 'Kids', 'sort_order' => 3],
            ['name' => 'Accessories', 'sort_order' => 4],
        ];

        foreach ($categories as $cat) {
            Category::factory()->create([
                ...$cat,
                'slug' => Str::slug($cat['name']),
            ]);
        }

        $brands = Brand::factory()->count(5)->create();
        $categoryIds = Category::pluck('id');

        Product::factory()->count(48)->create([
            'category_id' => fn () => $categoryIds->random(),
            'brand_id' => fn () => $brands->random()->id,
        ])->each(function (Product $product) {
            $colors = ['Black', 'White', 'Navy', 'Maroon'];
            foreach (config('fashion.sizes') as $size) {
                ProductVariant::query()->create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'color' => fake()->randomElement($colors),
                    'sku' => $product->sku.'-'.$size,
                    'stock' => fake()->numberBetween(5, 30),
                    'status' => RecordStatus::Active,
                ]);
            }
        });
    }
}
