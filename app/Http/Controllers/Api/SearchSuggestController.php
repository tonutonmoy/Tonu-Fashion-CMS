<?php

namespace App\Http\Controllers\Api;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::query()
            ->where('status', RecordStatus::Active)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'slug', 'effective_price', 'sku']);

        return response()->json($products->map(fn (Product $product) => [
            'label' => $product->name,
            'meta' => $product->sku ?: format_bdt($product->effective_price),
            'url' => route('products.show', $product->slug),
        ]));
    }
}
