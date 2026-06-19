<?php

namespace App\Repositories\Eloquent;

use App\Enums\RecordStatus;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getActiveOrdered(): Collection
    {
        return $this->model->newQuery()
            ->where('status', RecordStatus::Active)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }

    public function paginateAdmin(?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $paginator = $this->model->newQuery()
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);

        return $paginator;
    }
}
