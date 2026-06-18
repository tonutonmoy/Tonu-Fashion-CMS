<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Post;

    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function paginatePublished(int $perPage = 12): LengthAwarePaginator;

    public function getPublishedFeatured(int $limit = 3): Collection;

    public function getRelated(Post $post, int $limit = 4): Collection;
}
