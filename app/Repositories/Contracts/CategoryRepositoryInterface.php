<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveOrdered(): Collection;

    public function findBySlug(string $slug): ?Category;

    public function paginateAdmin(int $perPage = 15): LengthAwarePaginator;
}
