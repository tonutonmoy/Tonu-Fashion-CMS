<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ShopController extends Controller
{
    use RendersThemeViews;

    public function __construct(
        private ProductRepositoryInterface $products,
        private CategoryRepositoryInterface $categories,
        private BrandRepositoryInterface $brands,
        private SeoService $seo
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->all();
        $catalog = $this->catalogMeta();
        $products = $this->paginateShop($filters);

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html' => view('themes.shared.partials.shop-products', compact('products'))->render(),
                'total' => $products->total(),
            ]);
        }

        return $this->themeView('shop', [
            'products' => $products,
            'categories' => $catalog['categories'],
            'brands' => $catalog['brands'],
            'filters' => $filters,
            'priceBounds' => $catalog['priceBounds'],
            'seo' => $this->seo->meta(['title' => 'Shop | '.setting('name', config('app.name'))]),
        ]);
    }

    public function search(Request $request): View
    {
        $query = trim((string) $request->get('q', ''));

        if ($query !== '') {
            return redirect()->route('shop.index', ['q' => $query]);
        }

        $catalog = $this->catalogMeta();

        return $this->themeView('shop', [
            'products' => $this->paginateShop([]),
            'categories' => $catalog['categories'],
            'brands' => $catalog['brands'],
            'filters' => [],
            'priceBounds' => $catalog['priceBounds'],
            'seo' => $this->seo->meta(['title' => 'Shop | '.setting('name', config('app.name'))]),
        ]);
    }

    private function catalogMeta(): array
    {
        return Cache::remember('shop.catalog_meta', 600, fn () => [
            'categories' => $this->categories->getActiveOrdered(),
            'brands' => $this->brands->getActive(),
            'priceBounds' => $this->products->getPriceBounds(),
        ]);
    }

    private function paginateShop(array $filters)
    {
        $perPage = config('fashion.pagination.products');
        $hasFilters = collect($filters)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty();

        if ($hasFilters) {
            return $this->products->paginateShop($filters, $perPage);
        }

        return Cache::remember('shop.products.page1.'.$perPage, 300, fn () => $this->products->paginateShop([], $perPage));
    }
}
