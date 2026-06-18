<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Product;

    public function getFeatured(int $limit = 8): Collection;

    public function paginateShop(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(string $query, int $perPage = 12): LengthAwarePaginator;

    public function getPriceBounds(): array;

    public function getRelated(Product $product, int $limit = 8): Collection;
}
