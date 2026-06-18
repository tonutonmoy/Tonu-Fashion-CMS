<?php

namespace App\Repositories\Contracts;

use App\Enums\MenuLocation;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;

interface MenuRepositoryInterface
{
    public function findByLocation(MenuLocation $location): ?Menu;

    public function allWithItems(): Collection;

    public function syncItems(Menu $menu, array $items): void;
}
