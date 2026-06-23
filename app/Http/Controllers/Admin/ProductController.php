<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use App\Services\FlashSaleService;
use App\Services\VariantCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private CategoryRepositoryInterface $categories,
        private BrandRepositoryInterface $brands,
        private ProductService $productService,
        private VariantCatalogService $variantCatalog,
        private FlashSaleService $flashSale,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.products.index', [
            'products' => $this->products->paginateAdmin($request->all()),
            'categories' => $this->categories->getActiveOrdered(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => $this->categories->getActiveOrdered(),
            'brands' => $this->brands->getActive(),
            'sizes' => $this->variantCatalog->sizes(),
            'colors' => $this->variantCatalog->colors(),
            'flashSaleActive' => $this->flashSale->isSectionEnabledInBuilder(),
            'flashDiscount' => (int) ($this->flashSale->builderSettings()['discount'] ?? 20),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->variantCatalog->sync(
            $request->input('variant_catalog_sizes', []),
            $request->input('variant_catalog_colors', []),
        );

        $this->productService->create(
            $request->validated(),
            $request->file('images', []),
            $request->input('variants', []),
            $request->file('variants', [])
        );

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'), 'variants']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->categories->getActiveOrdered(),
            'brands' => $this->brands->getActive(),
            'sizes' => $this->variantCatalog->sizes($product),
            'colors' => $this->variantCatalog->colors($product),
            'flashSaleActive' => $this->flashSale->isSectionEnabledInBuilder(),
            'flashDiscount' => (int) ($this->flashSale->builderSettings()['discount'] ?? 20),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->variantCatalog->sync(
            $request->input('variant_catalog_sizes', []),
            $request->input('variant_catalog_colors', []),
        );

        $this->productService->update(
            $product->id,
            $request->validated(),
            $request->file('images', []),
            $request->input('variants', []),
            $request->input('remove_images', []),
            $request->file('variants', [])
        );

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product->id);

        return back()->with('success', 'Product deleted.');
    }
}
