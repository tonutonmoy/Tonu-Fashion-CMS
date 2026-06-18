<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\SettingRepositoryInterface;

class VariantCatalogService
{
    public function __construct(private SettingRepositoryInterface $settings) {}

    public function sizes(?Product $product = null): array
    {
        return $this->mergeWithProduct($this->storedSizes(), $product, 'size');
    }

    public function colors(?Product $product = null): array
    {
        return $this->mergeWithProduct($this->storedColors(), $product, 'color');
    }

    public function sync(array $sizes, array $colors): void
    {
        $this->settings->setMany('catalog', [
            'variant_sizes' => ['value' => $this->normalizeList($sizes), 'type' => 'json'],
            'variant_colors' => ['value' => $this->normalizeList($colors), 'type' => 'json'],
        ]);
    }

    private function storedSizes(): array
    {
        $stored = $this->settings->get('catalog', 'variant_sizes');

        if (is_array($stored) && $stored !== []) {
            return $this->normalizeList($stored);
        }

        return $this->normalizeList(config('fashion.sizes', []));
    }

    private function storedColors(): array
    {
        $stored = $this->settings->get('catalog', 'variant_colors');

        if (is_array($stored) && $stored !== []) {
            return $this->normalizeList($stored);
        }

        return $this->normalizeList(config('fashion.colors', []));
    }

    private function mergeWithProduct(array $catalog, ?Product $product, string $field): array
    {
        if (! $product) {
            return $catalog;
        }

        $fromVariants = $product->variants
            ->pluck($field)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values()
            ->all();

        return $this->normalizeList([...$catalog, ...$fromVariants]);
    }

    private function normalizeList(array $values): array
    {
        $normalized = [];

        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            if (! in_array($value, $normalized, true)) {
                $normalized[] = $value;
            }
        }

        return $normalized;
    }
}
