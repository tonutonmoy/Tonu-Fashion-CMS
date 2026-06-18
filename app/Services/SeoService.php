<?php

namespace App\Services;

use App\Enums\RecordStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class SeoService
{
    public function __construct(private SettingRepositoryInterface $settings) {}

    public function meta(array $overrides = []): array
    {
        $seo = $this->seoSettings();

        $defaults = [
            'title' => $seo['default_meta_title'] ?? setting('name', config('app.name')),
            'description' => $seo['default_meta_description'] ?? setting('meta_description', 'Premium fashion store in Bangladesh'),
            'og_image' => $this->absoluteUrl(setting('og_image') ?? setting('logo')),
            'canonical' => URL::current(),
            'twitter_handle' => $seo['twitter_handle'] ?? null,
            'og_type' => 'website',
        ];

        return array_merge($defaults, $overrides);
    }

    public function themeMeta(array $overrides = []): array
    {
        $theme = app(ThemeService::class)->settings();

        return $this->meta([
            'title' => $theme->meta_title ?: ($this->seoSettings()['default_meta_title'] ?? setting('name')),
            'description' => $theme->meta_description ?: ($this->seoSettings()['default_meta_description'] ?? ''),
            'og_image' => $this->absoluteUrl($theme->og_image ?? setting('logo')),
            'json_ld' => $theme->json_ld_schema ?? app(ThemeService::class)->defaultJsonLd(),
            ...$overrides,
        ]);
    }

    public function productMeta(Product $product): array
    {
        $breadcrumbs = $this->breadcrumbSchema([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Shop', 'url' => route('shop.index')],
            ['name' => $product->category?->name ?? 'Category', 'url' => $product->category ? route('categories.show', $product->category) : route('shop.index')],
            ['name' => $product->name, 'url' => route('products.show', $product)],
        ]);

        return $this->meta([
            'title' => $product->meta_title ?: $product->name.' | '.setting('name', config('app.name')),
            'description' => $product->meta_description ?: $product->short_description,
            'og_image' => $this->absoluteUrl($product->og_image ?? $product->primary_image),
            'canonical' => route('products.show', $product),
            'og_type' => 'product',
            'json_ld' => [$this->productSchema($product), $breadcrumbs],
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function categoryMeta(Category $category): array
    {
        $breadcrumbs = $this->breadcrumbSchema([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Shop', 'url' => route('shop.index')],
            ['name' => $category->name, 'url' => route('categories.show', $category)],
        ]);

        return $this->meta([
            'title' => $category->meta_title ?: $category->name.' | '.setting('name', config('app.name')),
            'description' => $category->meta_description,
            'og_image' => $this->absoluteUrl($category->og_image ?? $category->image),
            'canonical' => route('categories.show', $category),
            'json_ld' => $breadcrumbs,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function productSchema(Product $product): array
    {
        $inStock = $product->inStock();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description ?? $product->description,
            'sku' => $product->sku,
            'image' => $product->primary_image ? image_url($product->primary_image) : null,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand?->name ?? setting('name', config('app.name')),
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('products.show', $product),
                'priceCurrency' => config('fashion.currency_code', 'BDT'),
                'price' => (float) $product->effective_price,
                'availability' => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
            ],
            'aggregateRating' => $product->review_count > 0 ? [
                '@type' => 'AggregateRating',
                'ratingValue' => (float) $product->avg_rating,
                'reviewCount' => (int) $product->review_count,
            ] : null,
        ];
    }

    public function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->toArray(),
        ];
    }

    public function sitemapUrls(): array
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'lastmod' => now()->toAtomString()],
            ['loc' => route('shop.index'), 'priority' => '0.9', 'lastmod' => now()->toAtomString()],
        ];

        Category::query()->where('status', RecordStatus::Active)->each(function ($category) use (&$urls) {
            $urls[] = [
                'loc' => route('categories.show', $category),
                'priority' => '0.8',
                'lastmod' => $category->updated_at?->toAtomString(),
            ];
        });

        Brand::query()->where('status', RecordStatus::Active)->each(function ($brand) use (&$urls) {
            $urls[] = [
                'loc' => route('brands.show', $brand),
                'priority' => '0.7',
                'lastmod' => $brand->updated_at?->toAtomString(),
            ];
        });

        Product::query()->where('status', RecordStatus::Active)->each(function ($product) use (&$urls) {
            $urls[] = [
                'loc' => route('products.show', $product),
                'priority' => '0.8',
                'lastmod' => $product->updated_at?->toAtomString(),
            ];
        });

        return $urls;
    }

    public function robotsTxt(): string
    {
        return Cache::remember('seo_robots_txt', 3600, function () {
            $custom = $this->settings->get('seo', 'robots_txt');

            if ($custom) {
                return str_replace('{sitemap}', route('sitemap'), $custom);
            }

            return implode("\n", [
                'User-agent: *',
                'Allow: /',
                'Disallow: /admin',
                'Disallow: /checkout',
                'Sitemap: '.route('sitemap'),
            ]);
        });
    }

    private function seoSettings(): array
    {
        return $this->settings->getByGroup('seo')->pluck('value', 'key')->toArray();
    }

    private function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return image_url($path);
    }
}
