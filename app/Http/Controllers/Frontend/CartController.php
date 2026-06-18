<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    use RendersThemeViews;

    public function __construct(private CartService $cart) {}

    public function index(): View
    {
        return $this->themeView('cart', [
            'items' => $this->cart->getItems(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function store(CartRequest $request): RedirectResponse
    {
        $product = Product::query()->findOrFail($request->product_id);
        $variant = $request->product_variant_id
            ? ProductVariant::query()->findOrFail($request->product_variant_id)
            : null;

        $quantity = $request->integer('quantity', 1);
        $lineTotal = ($variant ? $variant->price : $product->effective_price) * $quantity;

        $this->cart->add($product, $quantity, $variant);
        $context = app(\App\Services\MarketingEventService::class)->trackAddToCart($product, $quantity, $lineTotal);

        return back()->with('success', 'Added to cart.')
            ->with('marketing_add_to_cart', [
                'product' => [
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'price' => (float) $product->effective_price,
                ],
                'quantity' => $quantity,
                'value' => $lineTotal,
                'event_id' => $context['event_id'],
            ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);
        $this->cart->update($id, $request->integer('quantity'));

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->cart->remove($id);

        return back()->with('success', 'Item removed.');
    }
}
