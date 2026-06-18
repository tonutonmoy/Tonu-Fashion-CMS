<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\SeoService;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private SeoService $seo
    ) {}

    public function show(Brand $brand): View
    {
        return view('frontend.brands.show', [
            'brand' => $brand,
            'products' => $this->products->paginateShop(['brand' => $brand->slug]),
            'seo' => $this->seo->meta(['title' => $brand->name.' | '.setting('store', 'name')]),
        ]);
    }
}
