<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOrderNumber(string $orderNumber): ?Order;

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator;

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator;
}
