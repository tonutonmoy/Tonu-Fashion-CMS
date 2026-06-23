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

        return $query->orderBy('sort_order')->orderBy('name')->paginate($perPage)->withQueryString();
    }
}
