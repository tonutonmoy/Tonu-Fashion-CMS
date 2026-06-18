<?php

namespace App\Repositories\Eloquent;

use App\Enums\MenuLocation;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MenuRepository implements MenuRepositoryInterface
{
    public function findByLocation(MenuLocation $location): ?Menu
    {
        return Menu::query()
            ->with(['allItems' => fn ($q) => $q->with('page', 'children.page')])
            ->where('location', $location->value)
            ->first();
    }

    public function allWithItems(): Collection
    {
        return Menu::query()->with('allItems.page')->orderBy('name')->get();
    }

    public function syncItems(Menu $menu, array $items): void
    {
        DB::transaction(function () use ($menu, $items) {
            MenuItem::query()->where('menu_id', $menu->id)->delete();

            $this->insertItems($menu->id, $items);
        });
    }

    private function insertItems(int $menuId, array $items, ?int $parentId = null): void
    {
        foreach ($items as $order => $item) {
            $record = MenuItem::query()->create([
                'menu_id' => $menuId,
                'parent_id' => $parentId,
                'title' => $item['title'],
                'url' => $item['url'] ?? null,
                'page_id' => $item['page_id'] ?? null,
                'sort_order' => $order + 1,
                'open_in_new_tab' => ! empty($item['open_in_new_tab']),
            ]);

            if (! empty($item['children'])) {
                $this->insertItems($menuId, $item['children'], $record->id);
            }
        }
    }
}
