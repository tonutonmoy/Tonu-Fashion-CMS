<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private ImageService $images,
        private StorefrontCacheService $cache,
        private FlashSaleService $flashSale,
    ) {}

    public function create(array $data, array $images = [], array $variants = [], array $variantFiles = []): Product
    {
        return DB::transaction(function () use ($data, $images, $variants, $variantFiles) {
            $data = $this->flashSale->applyProductPricing($data);
            $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name']);
            $data['sku'] = $this->resolveSku($data['sku'] ?? null, $data['name'], $data['slug']);
            unset($data['primary_image_id']);

            $product = $this->products->create($data);

            $this->syncImages($product, $images);
            $this->syncVariants($product, $variants, $variantFiles);
            $this->bustCaches($product);

            return $product->load(['images', 'variants', 'category', 'brand']);
        });
    }

    public function update(
        int $id,
        array $data,
        array $images = [],
        array $variants = [],
        array $removeImageIds = [],
        array $variantFiles = []
    ): Product {
        return DB::transaction(function () use ($id, $data, $images, $variants, $removeImageIds, $variantFiles) {
            $product = $this->products->find($id);
            $primaryImageId = $data['primary_image_id'] ?? null;
            unset($data['primary_image_id']);

            $data = $this->flashSale->applyProductPricing(array_merge(
                [
                    'regular_price' => $product->regular_price,
                    'sale_price' => $product->sale_price,
                    'is_flash_sale' => $product->is_flash_sale,
                ],
                $data,
            ));

            if (! empty($data['slug'])) {
                $data['slug'] = $this->resolveSlug($data['slug'], $data['name'] ?? $product->name, $product->id);
            } elseif (isset($data['name']) && $data['name'] !== $product->name) {
                $data['slug'] = $this->resolveSlug(null, $data['name'], $product->id);
            }

            $product = $this->products->update($id, $data);

            if ($removeImageIds) {
                ProductImage::query()
                    ->where('product_id', $product->id)
                    ->whereIn('id', $removeImageIds)
                    ->each(fn ($img) => $this->images->delete($img->path, $img->variants));
                ProductImage::query()->whereIn('id', $removeImageIds)->delete();
            }

            $this->syncImages($product, $images);

            if ($primaryImageId) {
                $this->setPrimaryImage($product, (int) $primaryImageId);
            }

            $this->syncVariants($product, $variants, $variantFiles);
            $this->bustCaches($product);

            return $product->load(['images', 'variants', 'category', 'brand']);
        });
    }

    public function delete(int $id): bool
    {
        $product = $this->products->find($id);

        foreach ($product->images as $image) {
            $this->images->delete($image->path, $image->variants);
        }

        foreach ($product->variants as $variant) {
            $this->images->delete($variant->image);
        }

        $slug = $product->slug;
        $deleted = $this->products->delete($id);
        $this->cache->forgetProduct($slug);
        $this->cache->forgetHomepage();
        $this->cache->forgetShop();

        return $deleted;
    }

    private function bustCaches(Product $product): void
    {
        $this->cache->forgetProduct($product->slug);
        $this->cache->forgetHomepage();
        $this->cache->forgetShop();
    }

    private function resolveSlug(?string $slug, string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($slug ?: $name);

        return $this->uniqueSlug($base, $exceptId);
    }

    private function resolveSku(?string $sku, string $name, string $slug): string
    {
        $sku = strtoupper(trim((string) $sku));

        if ($sku !== '') {
            return $sku;
        }

        $base = strtoupper(preg_replace('/[^A-Z0-9]+/', '-', Str::slug($slug ?: $name)) ?? 'PROD');
        $base = trim($base, '-') ?: 'PROD';
        $base = Str::limit($base, 24, '');

        do {
            $candidate = $base.'-'.Str::upper(Str::random(4));
        } while (Product::query()->where('sku', $candidate)->exists());

        return $candidate;
    }

    private function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $original = $slug;
        $counter = 1;

        while (Product::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }

    private function syncImages(Product $product, array $images): void
    {
        if (empty($images)) {
            return;
        }

        $product->loadMissing('images');
        $startOrder = (int) ($product->images->max('sort_order') ?? -1) + 1;
        $hasPrimary = $product->images->contains(fn ($img) => $img->is_primary);

        foreach ($images as $index => $file) {
            if (! $file) {
                continue;
            }

            $uploaded = $this->images->uploadWithVariants(
                $file,
                'products/'.$product->id,
                null,
                (int) config('fashion.image.product_quality', 85),
            );

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $uploaded['path'],
                'variants' => $uploaded['variants'] ?? [],
                'alt' => $product->name,
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $startOrder + $index,
            ]);

            if (! $hasPrimary && $index === 0) {
                $hasPrimary = true;
            }
        }
    }

    private function setPrimaryImage(Product $product, int $imageId): void
    {
        $image = ProductImage::query()
            ->where('product_id', $product->id)
            ->where('id', $imageId)
            ->first();

        if (! $image) {
            return;
        }

        ProductImage::query()
            ->where('product_id', $product->id)
            ->update(['is_primary' => false]);

        $image->update(['is_primary' => true, 'sort_order' => 0]);
    }

    private function syncVariants(Product $product, array $variants, array $variantFiles = []): void
    {
        if (empty($variants)) {
            return;
        }

        $existingIds = collect($variants)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        if (! empty($existingIds)) {
            ProductVariant::query()
                ->where('product_id', $product->id)
                ->whereNotIn('id', $existingIds)
                ->each(function (ProductVariant $variant) {
                    $this->images->delete($variant->image);
                    $variant->delete();
                });
        } else {
            ProductVariant::query()
                ->where('product_id', $product->id)
                ->each(function (ProductVariant $variant) {
                    $this->images->delete($variant->image);
                    $variant->delete();
                });
        }

        foreach ($variants as $index => $variant) {
            $imagePath = null;
            $file = $variantFiles[$index]['image'] ?? null;

            if ($file) {
                $uploaded = $this->images->uploadWithVariants($file, 'products/'.$product->id.'/variants', ['large' => 800]);
                $imagePath = $uploaded['path'];
            }

            $payload = [
                'size' => $variant['size'] ?? null,
                'color' => $variant['color'] ?? null,
                'sku' => $variant['sku'] ?? $product->sku.'-'.Str::upper(Str::random(4)),
                'stock' => $variant['stock'] ?? 0,
                'price_adjustment' => $variant['price_adjustment'] ?? 0,
                'status' => $variant['status'] ?? 'active',
            ];

            if (! empty($variant['id'])) {
                $existing = ProductVariant::query()
                    ->where('product_id', $product->id)
                    ->where('id', $variant['id'])
                    ->first();

                if ($existing) {
                    if ($imagePath) {
                        $this->images->delete($existing->image);
                        $payload['image'] = $imagePath;
                    } elseif (! empty($variant['remove_image'])) {
                        $this->images->delete($existing->image);
                        $payload['image'] = null;
                    }

                    $existing->update($payload);

                    continue;
                }
            }

            if ($imagePath) {
                $payload['image'] = $imagePath;
            }

            ProductVariant::query()->create([
                'product_id' => $product->id,
                ...$payload,
            ]);
        }
    }
}
