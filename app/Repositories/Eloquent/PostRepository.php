<?php

namespace App\Repositories\Eloquent;

use App\Enums\ContentStatus;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Post
    {
        return $this->model->newQuery()
            ->with(['category', 'tags', 'author'])
            ->where('slug', $slug)
            ->first();
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $query = $this->model->newQuery()->with('category')->latest();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%");
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function paginatePublished(int $perPage = 12): LengthAwarePaginator
    {
        return $this->publishedQuery()->paginate($perPage);
    }

    public function getPublishedFeatured(int $limit = 3): Collection
    {
        return $this->publishedQuery()->limit($limit)->get();
    }

    public function getRelated(Post $post, int $limit = 4): Collection
    {
        return $this->publishedQuery()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->limit($limit)
            ->get();
    }

    private function publishedQuery()
    {
        return $this->model->newQuery()
            ->with(['category', 'tags'])
            ->where('status', ContentStatus::Published)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->latest('published_at');
    }
}
