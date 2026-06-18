<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductPageService;
use App\Services\ReviewService;
use App\Services\SeoService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    use RendersThemeViews;

    public function __construct(
        private ProductRepositoryInterface $products,
        private ProductPageService $productPage,
        private SeoService $seo,
        private WishlistService $wishlist,
        private ReviewService $reviews
    ) {}

    public function show(string $slug): View
    {
        $product = $this->productPage->findForShow($slug);

        if (! $product) {
            abort(404);
        }

        $sessionKey = 'product.view_event.'.$product->id;
        if (! session()->has($sessionKey)) {
            session()->put($sessionKey, (string) \Illuminate\Support\Str::uuid());
        }
        $eventId = session($sessionKey);

        dispatch(function () use ($product) {
            app(\App\Services\MarketingEventService::class)->trackViewContent($product);
        })->afterResponse();

        return $this->themeView('product', [
            'product' => $product,
            'relatedProducts' => $this->products->getRelated($product, 8),
            'inWishlist' => $this->wishlist->has($product),
            'seo' => $this->seo->productMeta($product),
            'marketingProduct' => [
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => (float) $product->effective_price,
            ],
            'viewContentEventId' => $eventId,
        ]);
    }

    public function related(string $slug): JsonResponse
    {
        $product = $this->productPage->findForShow($slug);

        if (! $product) {
            abort(404);
        }

        return response()->json([
            'html' => $this->productPage->relatedHtml($product),
        ]);
    }

    public function storeReview(ReviewRequest $request, Product $product): RedirectResponse
    {
        if (! Auth::user()->canAccessBlog()) {
            return back()->with('error', 'You are not allowed to submit reviews.');
        }

        $this->reviews->submit($product->id, Auth::id(), $request->validated());

        return back()->with('success', 'Review submitted for approval.');
    }
}
