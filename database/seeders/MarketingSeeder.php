<?php

namespace Database\Seeders;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SettingRepositoryInterface::class);

        $settings->setMany('marketing', [
            'facebook_pixel_id' => '',
            'facebook_access_token' => '',
            'facebook_dataset_id' => '',
            'test_event_code' => '',
            'ga_measurement_id' => '',
            'gtm_id' => '',
            'tiktok_pixel_id' => '',
        ]);

        $settings->setMany('shipping', [
            'inside_dhaka' => 80,
            'outside_dhaka' => 150,
            'free_shipping_limit' => 2000,
        ]);

        $settings->setMany('sms', [
            'sms_enabled' => ['value' => '0', 'type' => 'boolean'],
            'sms_api_key' => '',
            'sms_sender_id' => '',
            'notify_confirmed' => ['value' => '1', 'type' => 'boolean'],
            'notify_shipped' => ['value' => '1', 'type' => 'boolean'],
            'notify_delivered' => ['value' => '1', 'type' => 'boolean'],
            'notify_parcel_created' => ['value' => '1', 'type' => 'boolean'],
            'notify_returned' => ['value' => '1', 'type' => 'boolean'],
        ]);

        $settings->setMany('social_chat', [
            'whatsapp_enabled' => true,
            'whatsapp_number' => '8801700000000',
            'messenger_enabled' => false,
            'messenger_link' => '',
            'instagram_enabled' => true,
            'instagram_link' => 'https://instagram.com/fashionbd',
            'telegram_enabled' => false,
            'telegram_link' => '',
            'support_chat_enabled' => true,
        ]);

        $settings->setMany('seo', [
            'default_meta_title' => 'Fashion BD — Premium Fashion with COD',
            'default_meta_description' => 'Shop trendy fashion in Bangladesh. Cash on delivery nationwide. Free shipping on orders above ৳2000.',
            'twitter_handle' => '@fashionbd',
            'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /checkout\nSitemap: {sitemap}",
        ]);
    }
}
