<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $products = $this->products->paginateShop($filters, config('fashion.pagination.products'));
        $priceBounds = $this->products->getPriceBounds();

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html' => view('themes.shared.partials.shop-products', compact('products'))->render(),
                'total' => $products->total(),
            ]);
        }

        return $this->themeView('shop', [
            'products' => $products,
            'categories' => $this->categories->getActiveOrdered(),
            'brands' => $this->brands->getActive(),
            'filters' => $filters,
            'priceBounds' => $priceBounds,
            'seo' => $this->seo->meta(['title' => 'Shop | '.setting('store', 'name')]),
        ]);
    }

    public function search(Request $request): View
    {
        $query = trim((string) $request->get('q', ''));

        if ($query !== '') {
            return redirect()->route('shop.index', ['q' => $query]);
        }

        return $this->themeView('shop', [
            'products' => $this->products->paginateShop([], config('fashion.pagination.products')),
            'categories' => $this->categories->getActiveOrdered(),
            'brands' => $this->brands->getActive(),
            'filters' => [],
            'priceBounds' => $this->products->getPriceBounds(),
            'seo' => $this->seo->meta(['title' => 'Shop | '.setting('store', 'name')]),
        ]);
    }
}
