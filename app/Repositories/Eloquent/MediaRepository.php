<?php

namespace App\Repositories\Eloquent;

use App\Models\Media;
use App\Repositories\Contracts\MediaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MediaRepository extends BaseRepository implements MediaRepositoryInterface
{
    public function __construct(Media $model)
    {
        parent::__construct($model);
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $query = $this->model->newQuery()->latest();

        if (! empty($filters['folder'])) {
            $query->where('folder', $filters['folder']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn ($q) => $q->where('filename', 'like', "%{$search}%")->orWhere('alt', 'like', "%{$search}%"));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getByFolder(string $folder): Collection
    {
        return $this->model->newQuery()->where('folder', $folder)->latest()->get();
    }

    public function search(string $query, int $limit = 50): Collection
    {
        return $this->model->newQuery()
            ->where('filename', 'like', "%{$query}%")
            ->orWhere('alt', 'like', "%{$query}%")
            ->latest()
            ->limit($limit)
            ->get();
    }
}
