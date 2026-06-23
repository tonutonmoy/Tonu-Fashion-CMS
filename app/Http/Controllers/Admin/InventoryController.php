<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustInventoryRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventory,
    ) {}

    public function index(Request $request): View
    {
        $lowStockOnly = $request->boolean('low_stock');
        $rows = $this->inventory->variantRows($lowStockOnly, $request->get('search'));

        return view('admin.inventory.index', [
            'rows' => $rows,
            'totalStockValue' => $rows->sum('stock_value'),
            'lowStockOnly' => $lowStockOnly,
            'threshold' => InventoryService::LOW_STOCK_THRESHOLD,
        ]);
    }

    public function log(Request $request): View
    {
        $movements = $this->inventory->movementLog(200);

        $variantIds = $movements->pluck('product_variant_id')->filter()->unique()->values();
        $productIds = $movements->pluck('product_id')->filter()->unique()->values();

        $variants = ProductVariant::query()
            ->with('product:id,name')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy(fn ($v) => (string) $v->id);

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get(['id', 'name'])
            ->keyBy(fn ($p) => (string) $p->id);

        return view('admin.inventory.log', [
            'movements' => $movements,
            'variants' => $variants,
            'products' => $products,
        ]);
    }

    public function adjust(AdjustInventoryRequest $request): JsonResponse|RedirectResponse
    {
        try {
            if ($request->filled('variant_id')) {
                $this->inventory->adjust(
                    (string) $request->variant_id,
                    (int) $request->quantity,
                    (string) $request->note,
                    (string) $request->user()->id,
                );
            } else {
                $this->inventory->adjustProduct(
                    (string) $request->product_id,
                    (int) $request->quantity,
                    (string) $request->note,
                    (string) $request->user()->id,
                );
            }
        } catch (InsufficientStockException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

            return back()->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Stock updated successfully.']);
        }

        return back()->with('success', 'Stock updated successfully.');
    }
}
