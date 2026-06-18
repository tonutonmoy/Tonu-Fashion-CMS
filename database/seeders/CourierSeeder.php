<?php

namespace Database\Seeders;

use App\Enums\CourierType;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SettingRepositoryInterface::class);

        $data = [
            'default_courier' => CourierType::Steadfast->value,
            'auto_parcel_enabled' => false,
        ];

        foreach (CourierType::cases() as $courier) {
            $key = $courier->value;
            $data["{$key}_enabled"] = $key === 'steadfast';
            $data["{$key}_api_key"] = '';
            $data["{$key}_secret_key"] = '';
            $data["{$key}_base_url"] = config("couriers.{$key}.default_base_url");
        }

        $settings->setMany('courier', $data);
    }
}
