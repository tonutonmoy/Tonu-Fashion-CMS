<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\CourierParcel;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CourierDashboardService
{
    public function stats(): array
    {
        $today = now()->startOfDay();

        $todayOrders = Order::query()->where('created_at', '>=', $today)->count();
        $todayDeliveries = Order::query()
            ->where('delivered_at', '>=', $today)
            ->count();
        $delivered = Order::query()->where('status', OrderStatus::Delivered)->count();
        $returned = Order::query()->where('status', OrderStatus::Returned)->count();

        $totalParcels = CourierParcel::query()->count();
        $deliveredParcels = CourierParcel::query()->where('current_status', 'delivered')->count();
        $deliveryRate = $totalParcels > 0 ? round(($deliveredParcels / $totalParcels) * 100, 1) : 0;

        $courierPerformance = CourierParcel::query()
            ->select('courier_name', DB::raw('count(*) as total'))
            ->selectRaw("sum(case when current_status = 'delivered' then 1 else 0 end) as delivered")
            ->selectRaw("sum(case when current_status = 'returned' then 1 else 0 end) as returned")
            ->groupBy('courier_name')
            ->get()
            ->map(fn ($row) => [
                'courier' => $row->courier_name,
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'returned' => (int) $row->returned,
                'rate' => $row->total > 0 ? round(($row->delivered / $row->total) * 100, 1) : 0,
            ]);

        return [
            'today_orders' => $todayOrders,
            'today_deliveries' => $todayDeliveries,
            'delivered_orders' => $delivered,
            'return_orders' => $returned,
            'delivery_rate' => $deliveryRate,
            'courier_performance' => $courierPerformance,
        ];
    }
}
