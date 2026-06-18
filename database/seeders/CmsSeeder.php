<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\MenuLocation;
use App\Enums\RecordStatus;
use App\Models\BlogCategory;
use App\Models\CmsPage;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\MenuBuilderService;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        app(MenuBuilderService::class)->seedDefaults();

        $about = CmsPage::query()->firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'content' => '<p>Welcome to Fashion BD — your premium fashion destination in Bangladesh.</p>',
                'status' => ContentStatus::Published,
            ]
        );

        $contact = CmsPage::query()->firstOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact',
                'content' => '<p>Reach us at hello@fashionbd.com</p>',
                'status' => ContentStatus::Published,
            ]
        );

        BlogCategory::query()->firstOrCreate(
            ['slug' => 'fashion-tips'],
            ['name' => 'Fashion Tips', 'status' => RecordStatus::Active]
        );

        $this->seedMenu(MenuLocation::Header, [
            ['title' => 'Shop', 'url' => '/shop'],
            ['title' => 'Blog', 'url' => '/blog'],
            ['title' => 'About', 'page_id' => $about->id],
            ['title' => 'Contact', 'page_id' => $contact->id],
        ]);

        $this->seedMenu(MenuLocation::Footer, [
            ['title' => 'Shop', 'url' => '/shop'],
            ['title' => 'Blog', 'url' => '/blog'],
            ['title' => 'About Us', 'page_id' => $about->id],
            ['title' => 'Contact', 'page_id' => $contact->id],
        ]);
    }

    private function seedMenu(MenuLocation $location, array $items): void
    {
        $menu = Menu::query()->firstOrCreate(
            ['location' => $location->value],
            ['name' => $location->label()]
        );

        if (MenuItem::query()->where('menu_id', $menu->id)->exists()) {
            return;
        }

        foreach ($items as $i => $item) {
            MenuItem::query()->create([
                'menu_id' => $menu->id,
                'title' => $item['title'],
                'url' => $item['url'] ?? null,
                'page_id' => $item['page_id'] ?? null,
                'sort_order' => $i + 1,
            ]);
        }
    }
}
