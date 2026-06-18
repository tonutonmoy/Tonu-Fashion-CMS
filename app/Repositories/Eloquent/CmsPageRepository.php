<?php

namespace App\Repositories\Eloquent;

use App\Enums\ContentStatus;
use App\Models\CmsPage;
use App\Repositories\Contracts\CmsPageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CmsPageRepository extends BaseRepository implements CmsPageRepositoryInterface
{
    public function __construct(CmsPage $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?CmsPage
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $query = $this->model->newQuery()->latest();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"));
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
