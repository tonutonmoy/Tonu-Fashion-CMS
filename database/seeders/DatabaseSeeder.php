<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->call(MarketingSeeder::class);
        $this->call(CourierSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->seedUsers();
        $this->seedCatalog();
        $this->call(ThemeSeeder::class);
        $this->call(CmsSeeder::class);
        Coupon::factory()->create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 500,
            'expires_at' => now()->addMonths(3),
        ]);
    }

    private function seedSettings(): void
    {
        $settings = app(SettingRepositoryInterface::class);

        $settings->setMany('store', [
            'name' => 'Fashion BD',
            'phone' => '01700000000',
            'email' => 'hello@fashionbd.com',
            'address' => 'Gulshan, Dhaka, Bangladesh',
            'facebook_url' => 'https://facebook.com/fashionbd',
            'instagram_url' => 'https://instagram.com/fashionbd',
            'whatsapp_number' => '8801700000000',
            'meta_description' => 'Premium fashion e-commerce store in Bangladesh with cash on delivery.',
        ]);

    }

    private function seedUsers(): void
    {
        User::factory()->create([
            'name' => config('admin.name'),
            'email' => config('admin.email'),
            'phone' => config('admin.phone'),
            'password' => Hash::make(config('admin.password')),
            'role' => UserRole::SuperAdmin,
            'status' => RecordStatus::Active,
        ]);

        User::factory()->count(10)->create();
    }

    private function seedCatalog(): void
    {
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
