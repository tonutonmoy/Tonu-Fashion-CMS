<?php

namespace App\Repositories\Contracts;

use App\Models\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CouponRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $code): ?Coupon;

    public function paginateAdmin(int $perPage = 15): LengthAwarePaginator;
}
