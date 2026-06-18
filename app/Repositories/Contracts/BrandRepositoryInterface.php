<?php

namespace App\Repositories\Contracts;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface extends BaseRepositoryInterface
{
    public function getActive(): Collection;

    public function findBySlug(string $slug): ?Brand;

    public function paginateAdmin(int $perPage = 15): LengthAwarePaginator;
}
