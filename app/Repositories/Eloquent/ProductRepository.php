<?php

namespace App\Repositories\Eloquent;

use App\Enums\RecordStatus;
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
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters['category']));
        }

        if (! empty($filters['brand'])) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $filters['brand']));
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
            $query->whereRaw('COALESCE(sale_price, regular_price) >= ?', [$filters['min_price']]);
        }

        if (! empty($filters['max_price'])) {
            $query->whereRaw('COALESCE(sale_price, regular_price) <= ?', [$filters['max_price']]);
        }

        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, regular_price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, regular_price) DESC'),
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
        $bounds = $this->model->newQuery()
            ->where('status', RecordStatus::Active)
            ->selectRaw('MIN(COALESCE(sale_price, regular_price)) as min_price')
            ->selectRaw('MAX(COALESCE(sale_price, regular_price)) as max_price')
            ->first();

        $min = (int) floor((float) ($bounds->min_price ?? 0));
        $max = (int) ceil((float) ($bounds->max_price ?? 10000));

        if ($max <= $min) {
            $max = $min + 1000;
        }

        return ['min' => $min, 'max' => $max];
    }

    public function getRelated(Product $product, int $limit = 8): Collection
    {
        return $this->model->newQuery()
            ->with(['images', 'category', 'brand'])
            ->where('status', RecordStatus::Active)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
                if ($product->brand_id) {
                    $query->orWhere('brand_id', $product->brand_id);
                }
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
