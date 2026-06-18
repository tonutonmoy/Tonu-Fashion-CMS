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

    public function paginateAdmin(?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        return $this->model->newQuery()
            ->withCount('products')
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
