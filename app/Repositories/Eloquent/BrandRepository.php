<?php

namespace App\Repositories\Eloquent;

use App\Enums\RecordStatus;
use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    public function getActive(): Collection
    {
        return $this->model->newQuery()
            ->where('status', RecordStatus::Active)
            ->orderBy('name')
            ->get();
    }

    public function findBySlug(string $slug): ?Brand
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $query = $this->model->newQuery()->withCount('products');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('id')->paginate($perPage)->withQueryString();
    }
}
