<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MediaRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateAdmin(array $filters = [], int $perPage = 24): LengthAwarePaginator;

    public function getByFolder(string $folder): Collection;

    public function search(string $query, int $limit = 50): Collection;
}
