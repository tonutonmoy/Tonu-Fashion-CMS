<?php

namespace App\Repositories\Eloquent;

use App\Enums\RecordStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->newQuery()
            ->with(['images', 'variants', 'category', 'brand', 'approvedReviews.user'])
            ->where('slug', $slug)
            ->first();
    }

    public function getFeatured(int $limit = 8): Collection
    {
        return $this->model->newQuery()
            ->with(['images', 'category'])
            ->where('status', RecordStatus::Active)
            ->where('featured', true)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function paginateShop(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['images', 'category', 'brand'])
            ->where('status', RecordStatus::Active);

        if (! empty($filters['category'])) {
            $categoryId = Category::query()->where('slug', $filters['category'])->value('id');
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        }

        if (! empty($filters['brand'])) {
            $brandId = Brand::query()->where('slug', $filters['brand'])->value('id');
            if ($brandId) {
                $query->where('brand_id', $brandId);
            }
        }

        if (! empty($filters['featured'])) {
            $query->where('featured', true);
        }

        if (! empty($filters['q'])) {
            $term = $filters['q'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%");
            });
        }

        if (! empty($filters['min_price'])) {
            $query->where('effective_price', '>=', (float) $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('effective_price', '<=', (float) $filters['max_price']);
        }

        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_asc' => $query->orderBy('effective_price'),
            'price_desc' => $query->orderByDesc('effective_price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $query = $this->model->newQuery()->with(['category', 'brand', 'images']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function search(string $query, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['images', 'category'])
            ->where('status', RecordStatus::Active)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getPriceBounds(): array
    {
        $prices = $this->model->newQuery()
            ->where('status', RecordStatus::Active)
            ->pluck('effective_price');

        if ($prices->isEmpty()) {
            return ['min' => 0, 'max' => 10000];
        }

        $min = (int) floor((float) $prices->min());
        $max = (int) ceil((float) $prices->max());

        if ($max <= $min) {
            $max = $min + 1000;
        }

        return ['min' => $min, 'max' => $max];
    }

    public function getRelated(Product $product, int $limit = 8): Collection
    {
        $candidates = $this->model->newQuery()
            ->with(['images', 'category', 'brand'])
            ->where('status', RecordStatus::Active)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
                if ($product->brand_id) {
                    $query->orWhere('brand_id', $product->brand_id);
                }
            })
            ->latest()
            ->limit(max($limit * 4, 24))
            ->get();

        if ($candidates->count() < $limit) {
            $extra = $this->model->newQuery()
                ->with(['images', 'category', 'brand'])
                ->where('status', RecordStatus::Active)
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $candidates->pluck('id')->all())
                ->latest()
                ->limit($limit - $candidates->count())
                ->get();

            $candidates = $candidates->concat($extra);
        }

        return $candidates->shuffle()->take($limit)->values();
    }
}
