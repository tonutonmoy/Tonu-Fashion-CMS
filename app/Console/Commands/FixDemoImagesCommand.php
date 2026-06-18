<?php

namespace App\Console\Commands;

use App\Models\HomepageSection;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FixDemoImagesCommand extends Command
{
    protected $signature = 'demo:fix-images';

    protected $description = 'Replace broken demo product image URLs with local placeholders';

    public function handle(): int
    {
        $placeholder = 'images/placeholder-product.svg';

        $updated = ProductImage::query()
            ->where(function ($q) {
                $q->where('path', 'like', '%ibb.co%')
                    ->orWhere('path', 'like', '%imgbb%')
                    ->orWhere('path', 'like', '%picsum.photos%');
            })
            ->update(['path' => $placeholder]);

        Cache::forget('homepage.page_data.en');
        Cache::forget('homepage.page_data.bn');
        Cache::forget('storefront.layout');
        Cache::forget('shop.catalog_meta');
        Cache::forget('shop.products.page1.'.config('fashion.pagination.products'));

        $hero = HomepageSection::query()->where('section_key', 'hero_slider')->first();
        if ($hero) {
            $settings = $hero->settings ?? [];
            $media = collect($settings['media'] ?? [])->map(function (array $item) use ($placeholder) {
                if (! empty($item['desktop_image']) && (
                    str_contains((string) $item['desktop_image'], 'ibb.co')
                    || str_contains((string) $item['desktop_image'], 'unsplash.com')
                )) {
                    $item['desktop_image'] = $placeholder;
                }

                return $item;
            })->all();
            $settings['media'] = $media;
            $hero->update(['settings' => $settings]);
        }

        $this->info("Updated {$updated} product images.");

        return self::SUCCESS;
    }
}
