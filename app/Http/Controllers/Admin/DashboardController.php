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
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private CourierDashboardService $courierStats,
        private ActivityLogService $activity,
    ) {}

    public function index(): View
    {
        $stats = Cache::remember('admin.dashboard.stats', 120, function () {
            return [
                'orders' => Order::query()->count(),
                'pending_orders' => Order::query()->where('status', OrderStatus::Pending)->count(),
                'products' => Product::query()->count(),
                'customers' => User::query()->where('role', UserRole::Customer)->count(),
                'revenue' => Order::query()
                    ->whereIn('status', [
                        OrderStatus::Delivered,
                        OrderStatus::Courier,
                        OrderStatus::Payment,
                        OrderStatus::CallingStage,
                    ])
                    ->sum('total'),
            ];
        });

        return view('admin.dashboard', [
            'stats' => $stats,
            'courier' => Cache::remember('admin.dashboard.courier', 120, fn () => $this->courierStats->stats()),
            'activityLogs' => $this->activity->recent(8),
            'recentOrders' => Cache::remember('admin.dashboard.recent_orders', 60, fn () => Order::query()
                ->with(['user:id,name', 'courierParcel:id,order_id,current_status'])
                ->latest()
                ->limit(10)
                ->get()),
        ]);
    }
}
