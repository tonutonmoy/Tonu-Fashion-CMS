<?php

namespace Database\Factories;

use App\Enums\RecordStatus;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'status' => RecordStatus::Active,
            'sort_order' => fake()->numberBetween(0, 100),
            'meta_title' => ucfirst($name).' | Fashion',
            'meta_description' => fake()->sentence(),
        ];
    }
}
