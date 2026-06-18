<?php

namespace App\Repositories\Eloquent;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    public function getApprovedForProduct(int $productId): Collection
    {
        return $this->model->newQuery()
            ->with('user')
            ->where('product_id', $productId)
            ->where('is_approved', true)
            ->latest()
            ->get();
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $query = $this->model->newQuery()->with(['product', 'user']);

        if (isset($filters['is_approved'])) {
            $query->where('is_approved', (bool) $filters['is_approved']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }
}
