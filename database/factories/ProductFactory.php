<?php

namespace Database\Factories;

use App\Enums\RecordStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $regular = fake()->numberBetween(500, 5000);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'sku' => 'SKU-'.strtoupper(Str::random(8)),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'regular_price' => $regular,
            'sale_price' => fake()->boolean(30) ? $regular - fake()->numberBetween(50, 500) : null,
            'stock' => fake()->numberBetween(0, 100),
            'featured' => fake()->boolean(20),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'status' => RecordStatus::Active,
        ];
    }
}
