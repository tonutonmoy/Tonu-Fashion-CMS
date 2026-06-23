<?php

namespace App\Services;

use App\Enums\HomepageSectionKey;
use App\Enums\RecordStatus;
use App\Models\Product;
use App\Repositories\Contracts\HomepageSectionRepositoryInterface;
use Illuminate\Support\Collection;

class FlashSaleService
{
    private ?array $settings = null;

    private ?bool $sectionEnabled = null;

    public function __construct(
        private HomepageSectionRepositoryInterface $sections,
        private StorefrontCacheService $cache,
    ) {}

    public function isSectionEnabled(): bool
    {
        if ($this->sectionEnabled === null) {
            $section = $this->sections->findByKey(HomepageSectionKey::FlashSale->value);
            $this->sectionEnabled = (bool) ($section?->enabled ?? false);
        }

        return $this->sectionEnabled;
    }

    public function getSettings(): array
    {
        if ($this->settings === null) {
            $section = $this->sections->findByKey(HomepageSectionKey::FlashSale->value);
            $this->settings = $section?->settings ?? [];
        }

        return $this->settings;
    }

    public function isActive(): bool
    {
        if (! $this->isSectionEnabled()) {
            return false;
        }

        $settings = $this->getSettings();
        $start = $this->parseBoundary($settings['start_at'] ?? $settings['start_date'] ?? null, startOfDay: true);
        $end = $this->parseBoundary($settings['end_at'] ?? $settings['end_date'] ?? null, startOfDay: false);

        if (! $start || ! $end) {
            return false;
        }

        return now()->between($start, $end);
    }

    public function discountPercent(): int
    {
        return max(0, min(90, (int) ($this->getSettings()['discount'] ?? 0)));
    }

    public function discountedPrice(float $regularPrice): float
    {
        $discount = $this->discountPercent();

        if ($discount <= 0) {
            return round($regularPrice, 2);
        }

        return round($regularPrice * (100 - $discount) / 100, 2);
    }

    public function isProductInActiveSale(Product $product): bool
    {
        return $this->isActive() && (bool) $product->is_flash_sale;
    }

    public function resolveUnitPrice(Product $product): float
    {
        if ($this->isProductInActiveSale($product)) {
            return $this->discountedPrice((float) $product->regular_price);
        }

        return (float) ($product->sale_price ?? $product->regular_price);
    }

    public function applyProductPricing(array $data): array
    {
        $wantsFlash = (bool) ($data['flash_sale'] ?? $data['is_flash_sale'] ?? false);
        unset($data['flash_sale']);

        $data['is_flash_sale'] = $wantsFlash;

        if ($this->isActive() && $wantsFlash && isset($data['regular_price'])) {
            $data['sale_price'] = $this->discountedPrice((float) $data['regular_price']);
        }

        $regular = (float) ($data['regular_price'] ?? 0);
        $sale = array_key_exists('sale_price', $data) && $data['sale_price'] !== null
            ? (float) $data['sale_price']
            : null;

        $data['effective_price'] = ($this->isActive() && $wantsFlash)
            ? $this->discountedPrice($regular)
            : ($sale ?? $regular);

        return $data;
    }

    public function getProducts(int $limit = 8): Collection
    {
        if (! $this->isActive()) {
            return collect();
        }

        $settings = $this->getSettings();
        $query = Product::query()
            ->with(['images', 'category'])
            ->where('status', RecordStatus::Active)
            ->where('stock', '>', 0)
            ->where('is_flash_sale', true);

        if (! empty($settings['product_ids'])) {
            $ids = array_values(array_filter(array_map('intval', $settings['product_ids'])));
            if ($ids !== []) {
                $query->whereIn('id', $ids)
                    ->orderByRaw('FIELD(id, '.implode(',', $ids).')');
            }
        } else {
            $query->latest();
        }

        return $query->limit($limit)->get();
    }

    public function syncEffectivePrices(): void
    {
        Product::query()
            ->where('is_flash_sale', true)
            ->each(function (Product $product) {
                $product->effective_price = $this->isActive()
                    ? $this->discountedPrice((float) $product->regular_price)
                    : (float) ($product->sale_price ?? $product->regular_price);
                $product->saveQuietly();
            });

        $this->cache->forgetHomepage();
    }

    public function endsAtIso(): ?string
    {
        $settings = $this->getSettings();
        $end = $this->parseBoundary($settings['end_at'] ?? $settings['end_date'] ?? null, startOfDay: false);

        return $end?->toIso8601String();
    }

    private function parseBoundary(?string $value, bool $startOfDay): ?\Carbon\Carbon
    {
        if (blank($value)) {
            return null;
        }

        $parsed = \Carbon\Carbon::parse($value);

        if (strlen((string) $value) <= 10) {
            return $startOfDay ? $parsed->startOfDay() : $parsed->endOfDay();
        }

        return $parsed;
    }
}
