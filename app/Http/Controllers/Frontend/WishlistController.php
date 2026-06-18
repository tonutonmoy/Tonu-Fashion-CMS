<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    use RendersThemeViews;

    public function __construct(private WishlistService $wishlist) {}

    public function index(): View
    {
        return $this->themeView('wishlist', [
            'items' => $this->wishlist->getItems(),
        ]);
    }

    public function toggle(Product $product): RedirectResponse
    {
        $added = $this->wishlist->toggle($product);

        return back()->with('success', $added ? 'Added to wishlist.' : 'Removed from wishlist.');
    }

    public function destroy(int $productId): RedirectResponse
    {
        $this->wishlist->remove($productId);

        return back()->with('success', 'Removed from wishlist.');
    }
}
