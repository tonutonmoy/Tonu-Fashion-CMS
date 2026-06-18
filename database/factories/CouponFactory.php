<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Enums\RecordStatus;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('SAVE##')),
            'type' => fake()->randomElement(CouponType::cases()),
            'value' => fake()->randomElement([10, 15, 20, 100, 200]),
            'min_order_amount' => fake()->optional()->numberBetween(500, 2000),
            'usage_limit' => fake()->optional()->numberBetween(10, 100),
            'used_count' => 0,
            'expires_at' => fake()->optional()->dateTimeBetween('+1 week', '+3 months'),
            'status' => RecordStatus::Active,
        ];
    }
}
