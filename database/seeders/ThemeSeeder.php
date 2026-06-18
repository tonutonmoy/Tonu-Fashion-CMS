<?php

namespace Database\Seeders;

use App\Models\FooterSetting;
use App\Models\ThemeSetting;
use App\Services\HomepageBuilderService;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        ThemeSetting::query()->firstOrCreate([], [
            'active_theme' => 'fashion-modern',
            'primary_color' => '#e11d48',
            'secondary_color' => '#1f2937',
            'accent_color' => '#f59e0b',
            'font_family' => 'Inter',
            'header_style' => 'default',
            'footer_style' => 'default',
            'button_radius' => '0.5rem',
            'container_width' => '80rem',
            'meta_title' => 'Fashion BD — Premium Fashion Store',
            'meta_description' => 'Shop the latest fashion trends in Bangladesh. Cash on delivery nationwide.',
        ]);

        FooterSetting::query()->firstOrCreate([], [
            'description' => 'Premium fashion for Bangladesh. Cash on delivery nationwide.',
            'address' => 'Gulshan, Dhaka, Bangladesh',
            'phone' => '01700000000',
            'email' => 'hello@fashionbd.com',
            'copyright_text' => '© Fashion BD. All rights reserved.',
        ]);

        app(HomepageBuilderService::class)->seedDefaults();

        $hero = \App\Models\HomepageSection::query()->where('section_key', 'hero_slider')->first();
        if ($hero) {
            $hero->update([
                'settings' => array_merge($hero->settings ?? [], [
                    'title' => 'New Season Collection',
                    'subtitle' => 'Premium fashion with cash on delivery across Bangladesh.',
                    'button_text' => 'Shop Now',
                    'button_link' => '/shop',
                    'show_title' => true,
                    'show_subtitle' => true,
                    'show_button' => true,
                    'media' => [[
                        'id' => 'hero-1',
                        'type' => 'image',
                        'desktop_image' => 'images/placeholder-product.svg',
                        'sort_order' => 1,
                    ]],
                ]),
            ]);
        }
    }
}
