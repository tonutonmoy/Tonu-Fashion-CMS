<?php

namespace Database\Seeders;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SettingRepositoryInterface::class);

        $settings->setMany('payment', [
            'cod_enabled' => true,
            'bkash_enabled' => false,
            'bkash_sandbox' => true,
            'bkash_app_key' => '',
            'bkash_app_secret' => '',
            'bkash_username' => '',
            'bkash_password' => '',
            'nagad_enabled' => false,
            'nagad_sandbox' => true,
            'nagad_merchant_id' => '',
            'nagad_app_key' => '',
            'nagad_app_secret' => '',
            'sslcommerz_enabled' => false,
            'sslcommerz_sandbox' => true,
            'sslcommerz_store_id' => '',
            'sslcommerz_app_secret' => '',
        ]);
    }
}
