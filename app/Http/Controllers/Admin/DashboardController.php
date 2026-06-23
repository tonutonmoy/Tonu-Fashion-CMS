<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Services\ActivityLogService;
use App\Services\CourierDashboardService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private CourierDashboardService $courierStats,
        private ActivityLogService $activity,
        private InventoryService $inventory,
    ) {}

    public function index(Request $request): View
    {
        $perPage = admin_per_page();

        $stats = Cache::remember('admin.dashboard.stats', 120, function () {
            return [
                'orders' => Order::query()->count(),
                'pending_orders' => Order::query()->where('status', OrderStatus::Pending)->count(),
                'products' => Product::query()->count(),
                'customers' => User::query()->where('role', UserRole::Customer)->count(),
            ];
        });

        $inventory = Cache::remember('admin.dashboard.inventory', 120, fn () => $this->inventory->summary());

        $lowStockPaginator = $this->inventory->variantRows()
            ->filter(fn (array $row) => $row['available_stock'] < $this->inventory->lowStockThreshold())
            ->values();

        $lowStockPage = max(1, (int) $request->get('low_stock_page', 1));
        $lowStockItems = $lowStockPaginator->forPage($lowStockPage, $perPage)->values();
        $lowStockTotal = $lowStockPaginator->count();

        $courierPerformance = collect(Cache::remember('admin.dashboard.courier', 120, fn () => $this->courierStats->stats())['courier_performance']);
        $courierPage = max(1, (int) $request->get('courier_page', 1));
        $courierRows = $courierPerformance->forPage($courierPage, $perPage)->values();

        return view('admin.dashboard', [
            'stats' => $stats,
            'inventory' => $inventory,
            'lowStockProducts' => $lowStockItems,
            'lowStockPage' => $lowStockPage,
            'lowStockTotal' => $lowStockTotal,
            'lowStockPerPage' => $perPage,
            'courier' => Cache::remember('admin.dashboard.courier', 120, fn () => $this->courierStats->stats()),
            'courierRows' => $courierRows,
            'courierPage' => $courierPage,
            'courierTotal' => $courierPerformance->count(),
            'courierPerPage' => $perPage,
            'activityLogs' => $this->activity->paginateAdmin($perPage, 'activity_page'),
            'recentOrders' => Order::query()
                ->with(['user:id,name', 'courierParcel:id,order_id,current_status'])
                ->latest()
                ->paginate($perPage, ['*'], 'orders_page')
                ->withQueryString(),
        ]);
    }
}
