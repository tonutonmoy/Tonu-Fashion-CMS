<?php

namespace App\Services;

use App\Enums\RecordStatus;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
class ProductPageService
{
    public function __construct(
        private ProductRepositoryInterface $products,
    ) {}

    public function findForShow(string $slug): ?Product
    {
        $product = $this->products->findBySlug($slug);

        if (! $product || $product->status !== RecordStatus::Active) {
            return null;
        }

        return $product;
    }

    public function relatedHtml(Product $product): string
    {
        $related = $this->products->getRelated($product, 8);

        if ($related->isEmpty()) {
            return '';
        }

        return view('themes.shared.partials.related-products', [
            'relatedProducts' => $related,
            'product' => $product,
        ])->render();
    }

    public function warmSlugs(array $slugs): void
    {
        foreach ($slugs as $slug) {
            $product = $this->findForShow($slug);
            if ($product) {
                $this->products->getRelated($product, 8);
            }
        }
    }
}
