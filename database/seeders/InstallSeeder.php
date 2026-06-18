<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InstallSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MarketingSeeder::class,
            CourierSeeder::class,
            PaymentSeeder::class,
            ThemeSeeder::class,
            DemoCatalogSeeder::class,
        ]);
    }
}
