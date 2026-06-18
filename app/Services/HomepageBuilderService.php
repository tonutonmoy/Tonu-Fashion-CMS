<?php

namespace App\Services;

use App\Enums\HomepageSectionKey;
use App\Enums\RecordStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Repositories\Contracts\HomepageSectionRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class HomepageBuilderService
{
    public function __construct(
        private HomepageSectionRepositoryInterface $sections,
        private ProductRepositoryInterface $products,
        private PostRepositoryInterface $posts,
        private BuilderPublishService $publish,
        private HeroBuilderService $hero
    ) {}

    public function getEnabledSectionKeys(): array
    {
        return $this->enabledSections()->pluck('section_key')->all();
    }

    public function getPageData(): array
    {
        $data = [];

        foreach ($this->enabledSections() as $section) {
            $data[$section->section_key] = $this->resolveSectionData($section);
        }

        return $data;
    }

    public function getAllSections(): Collection
    {
        return $this->publish->getEffectiveHomepageSections();
    }

    public function toggleSection(int $id, bool $enabled): void
    {
        $this->publish->toggleHomepageSection($id, $enabled);
    }

    public function updateSection(int $id, array $data): void
    {
        $this->publish->updateHomepageSection($id, $data);
    }

    public function reorderSections(array $orderedIds): void
    {
        $this->publish->reorderHomepageSections($orderedIds);
    }

    public function seedDefaults(): void
    {
        $defaults = [];

        foreach (HomepageSectionKey::cases() as $key) {
            $defaults[] = [
                'section_key' => $key->value,
                'title' => $key->label(),
                'enabled' => true,
                'sort_order' => $key->defaultSortOrder(),
                'settings' => $this->defaultSettingsFor($key),
            ];
        }

        $this->sections->syncDefaults($defaults);
    }

    private function resolveSectionData($section): array
    {
        $settings = $section->settings ?? [];

        return match ($section->section_key) {
            HomepageSectionKey::HeroSlider->value => [
                'config' => $this->hero->configFromSettings($settings),
                'settings' => $settings,
            ],
            HomepageSectionKey::Categories->value => [
                'categories' => $this->getCategories($settings),
                'settings' => $settings,
            ],
            HomepageSectionKey::FeaturedProducts->value => [
                'products' => $this->getProducts($settings, 'featured'),
                'settings' => $settings,
            ],
            HomepageSectionKey::NewArrivals->value => [
                'products' => Product::query()
                    ->with(['images', 'category'])
                    ->where('status', RecordStatus::Active)
                    ->latest()
                    ->limit($settings['limit'] ?? 8)
                    ->get(),
                'settings' => $settings,
            ],
            HomepageSectionKey::FlashSale->value => [
                'products' => $this->getFlashSaleProducts($settings),
                'settings' => $settings,
                'active' => $this->isFlashSaleActive($settings),
            ],
            HomepageSectionKey::BestSellers->value => [
                'products' => Product::query()
                    ->with(['images', 'category'])
                    ->where('status', RecordStatus::Active)
                    ->orderByDesc('review_count')
                    ->limit($settings['limit'] ?? 8)
                    ->get(),
                'settings' => $settings,
            ],
            HomepageSectionKey::CustomerReviews->value => [
                'reviews' => Review::query()
                    ->with(['user', 'product'])
                    ->where('is_approved', true)
                    ->latest()
                    ->limit($settings['limit'] ?? 6)
                    ->get(),
                'settings' => $settings,
            ],
            HomepageSectionKey::Blog->value => [
                'posts' => $this->posts->getPublishedFeatured($settings['limit'] ?? 3),
                'settings' => $settings,
            ],
            HomepageSectionKey::Faq->value => [
                'items' => collect($settings['items'] ?? []),
                'settings' => $settings,
            ],
            HomepageSectionKey::Newsletter->value => [
                'settings' => $settings,
            ],
            default => ['settings' => $settings],
        };
    }

    private function getCategories(array $settings): Collection
    {
        $query = Category::query()->where('status', RecordStatus::Active)->orderBy('sort_order');

        if (! empty($settings['category_ids'])) {
            $query->whereIn('id', $settings['category_ids']);
        }

        return $query->limit($settings['limit'] ?? 6)->get();
    }

    private function getProducts(array $settings, string $type): Collection
    {
        if (! empty($settings['product_ids'])) {
            return Product::query()
                ->with(['images', 'category'])
                ->whereIn('id', $settings['product_ids'])
                ->where('status', RecordStatus::Active)
                ->get();
        }

        if ($type === 'featured') {
            return $this->products->getFeatured($settings['limit'] ?? 8);
        }

        return collect();
    }

    private function getFlashSaleProducts(array $settings): Collection
    {
        if (! $this->isFlashSaleActive($settings)) {
            return collect();
        }

        return Product::query()
            ->with(['images', 'category'])
            ->where('status', RecordStatus::Active)
            ->whereNotNull('sale_price')
            ->latest()
            ->limit($settings['limit'] ?? 8)
            ->get();
    }

    private function isFlashSaleActive(array $settings): bool
    {
        if (empty($settings['start_date']) || empty($settings['end_date'])) {
            return false;
        }

        $now = now();
        $start = \Carbon\Carbon::parse($settings['start_date']);
        $end = \Carbon\Carbon::parse($settings['end_date']);

        return $now->between($start, $end);
    }

    private function defaultSettingsFor(HomepageSectionKey $key): array
    {
        return match ($key) {
            HomepageSectionKey::Categories => ['limit' => 6, 'category_ids' => []],
            HomepageSectionKey::FeaturedProducts => ['limit' => 8, 'product_ids' => []],
            HomepageSectionKey::NewArrivals => ['limit' => 8],
            HomepageSectionKey::FlashSale => [
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(7)->toDateString(),
                'discount' => 20,
                'show_countdown' => true,
                'limit' => 8,
            ],
            HomepageSectionKey::BestSellers => ['limit' => 8],
            HomepageSectionKey::CustomerReviews => ['limit' => 6],
            HomepageSectionKey::Blog => ['posts' => []],
            HomepageSectionKey::Faq => ['items' => [
                ['question' => 'Do you offer Cash on Delivery?', 'answer' => 'Yes, we deliver all over Bangladesh with COD.'],
                ['question' => 'What is the delivery time?', 'answer' => 'Inside Dhaka 1-2 days, outside Dhaka 3-5 days.'],
            ]],
            HomepageSectionKey::Newsletter => [
                'title' => 'Subscribe for exclusive offers',
                'subtitle' => 'Get updates on new arrivals and flash sales.',
            ],
            HomepageSectionKey::HeroSlider => [
                'title' => '',
                'subtitle' => null,
                'button_text' => null,
                'button_link' => null,
                'content_layout' => 'centered',
                'title_size' => 40,
                'subtitle_size' => 18,
                'button_size' => 14,
                'overlay_color' => '#000000',
                'autoplay_seconds' => 5,
                'media' => [],
            ],
            default => [],
        };
    }

    private function enabledSections(): Collection
    {
        if (should_use_builder_draft()) {
            return $this->publish->getEffectiveHomepageSections()
                ->where('enabled', true)
                ->sortBy('sort_order')
                ->values();
        }

        return $this->sections->getEnabledOrdered();
    }

    private function model(): HomepageSectionRepositoryInterface
    {
        return $this->sections;
    }
}
