<?php

namespace App\Repositories\Eloquent;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    public function __construct(Coupon $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): ?Coupon
    {
        return $this->model->newQuery()
            ->where('code', strtoupper($code))
            ->first();
    }

    public function paginateAdmin(?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        return $this->model->newQuery()->latest()->paginate($perPage);
    }
}
