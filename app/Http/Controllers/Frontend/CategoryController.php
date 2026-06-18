<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\SeoService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use RendersThemeViews;

    public function __construct(
        private ProductRepositoryInterface $products,
        private SeoService $seo
    ) {}

    public function show(Category $category): View
    {
        return $this->themeView('category', [
            'category' => $category,
            'products' => $this->products->paginateShop(['category' => $category->slug]),
            'seo' => $this->seo->categoryMeta($category),
        ]);
    }
}
