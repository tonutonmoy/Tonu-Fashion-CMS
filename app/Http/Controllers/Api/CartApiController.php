<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function show(): JsonResponse
    {
        return response()->json($this->payload());
    }

    public function store(CartRequest $request): JsonResponse
    {
        $product = Product::query()->findOrFail($request->product_id);
        $variant = $request->product_variant_id
            ? ProductVariant::query()->findOrFail($request->product_variant_id)
            : null;

        $quantity = $request->integer('quantity', 1);
        $this->cart->add($product, $quantity, $variant);

        return response()->json($this->payload('Added to cart.'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);
        $this->cart->update($id, $request->integer('quantity'));

        return response()->json($this->payload('Cart updated.'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->cart->remove($id);

        return response()->json($this->payload('Item removed.'));
    }

    private function payload(?string $message = null): array
    {
        $items = $this->cart->getItems()->map(fn ($item) => [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'name' => $item->product->name,
            'slug' => $item->product->slug,
            'image' => image_url($item->product->primary_image),
            'variant' => $item->variant?->display_name,
            'quantity' => $item->quantity,
            'line_total' => $item->line_total,
            'price_label' => format_bdt($item->line_total),
        ]);

        return [
            'message' => $message,
            'count' => $this->cart->count(),
            'subtotal' => $this->cart->subtotal(),
            'subtotal_label' => format_bdt($this->cart->subtotal()),
            'items' => $items,
            'checkout_url' => route('checkout.index'),
            'cart_url' => route('cart.index'),
        ];
    }
}
