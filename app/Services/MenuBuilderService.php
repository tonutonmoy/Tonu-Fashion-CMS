<?php

namespace App\Services;

use App\Enums\MenuLocation;
use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuBuilderService
{
    public function __construct(private MenuRepositoryInterface $menus) {}

    public function getTree(MenuLocation $location): Collection
    {
        return Cache::rememberForever("storefront.menu.{$location->value}", function () use ($location) {
            $menu = Menu::query()
                ->where('location', $location->value)
                ->with(['items' => fn ($q) => $q->with(['children.page', 'page'])])
                ->first();

            return $menu?->items ?? collect();
        });
    }

    public function getAllMenus(): Collection
    {
        return $this->menus->allWithItems();
    }

    public function saveMenu(MenuLocation $location, string $name, array $items): Menu
    {
        $menu = Menu::query()->firstOrCreate(
            ['location' => $location->value],
            ['name' => $name]
        );

        $menu->update(['name' => $name]);
        $this->menus->syncItems($menu, $items);
        Cache::forget("storefront.menu.{$location->value}");
        Cache::forget('storefront.layout');

        return $menu->fresh('allItems');
    }

    public function seedDefaults(): void
    {
        foreach (MenuLocation::cases() as $location) {
            Menu::query()->firstOrCreate(
                ['location' => $location->value],
                ['name' => $location->label()]
            );
        }
    }
}
