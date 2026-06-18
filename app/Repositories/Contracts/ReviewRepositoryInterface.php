<?php

namespace App\Repositories\Contracts;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function getApprovedForProduct(int $productId): Collection;

    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
