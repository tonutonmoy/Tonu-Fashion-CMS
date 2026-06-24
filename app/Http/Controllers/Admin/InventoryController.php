<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustInventoryRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventory,
        private SettingService $settings,
    ) {}

    public function index(Request $request): View
    {
        $lowStockOnly = $request->boolean('low_stock');
        $allGroups = $this->inventory->productGroupedRows($lowStockOnly, $request->get('search'));
        $perPage = admin_per_page();
        $page = $request->integer('page', 1);
        $groups = new LengthAwarePaginator(
            $allGroups->forPage($page, $perPage)->values(),
            $allGroups->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('admin.inventory.index', [
            'groups' => $groups,
            'totalStockValue' => $allGroups->sum('stock_value'),
            'lowStockCount' => $allGroups->filter(fn (array $g) => $g['available_stock'] < $this->inventory->lowStockThreshold())->count(),
            'lowStockOnly' => $lowStockOnly,
            'threshold' => $this->inventory->lowStockThreshold(),
        ]);
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('store'), 403);

        $request->validate([
            'low_stock_threshold' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $this->settings->updateStore([
            'low_stock_threshold' => (string) $request->integer('low_stock_threshold'),
        ]);

        Cache::forget('admin.dashboard.inventory');

        return back()->with('success', 'Inventory preferences saved.');
    }

    public function log(Request $request): View
    {
        $movements = $this->inventory->paginateMovementLog(admin_per_page(), $request->integer('page', 1));

        $variantIds = $movements->getCollection()->pluck('product_variant_id')->filter()->unique()->values();
        $productIds = $movements->getCollection()->pluck('product_id')->filter()->unique()->values();

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

        Cache::forget('admin.dashboard.inventory');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Stock updated successfully.']);
        }

        return back()->with('success', 'Stock updated successfully.');
    }
}
