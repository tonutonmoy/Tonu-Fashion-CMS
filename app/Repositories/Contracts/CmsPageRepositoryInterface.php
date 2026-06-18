<?php

namespace App\Repositories\Contracts;

use App\Models\CmsPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CmsPageRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?CmsPage;

    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
