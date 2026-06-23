<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\CourierParcel;
use App\Models\Order;

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

        $parcelStats = CourierParcel::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN LOWER(current_status) = 'delivered' THEN 1 ELSE 0 END) as delivered_count")
            ->first();

        $totalParcels = (int) ($parcelStats->total ?? 0);
        $deliveredParcels = (int) ($parcelStats->delivered_count ?? 0);
        $deliveryRate = $totalParcels > 0 ? round(($deliveredParcels / $totalParcels) * 100, 1) : 0;

        $courierPerformance = CourierParcel::query()
            ->select('courier_name')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN LOWER(current_status) = 'delivered' THEN 1 ELSE 0 END) as delivered")
            ->selectRaw("SUM(CASE WHEN LOWER(current_status) = 'returned' THEN 1 ELSE 0 END) as returned")
            ->groupBy('courier_name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'courier' => $row->courier_name,
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'returned' => (int) $row->returned,
                'rate' => $row->total > 0 ? round(((int) $row->delivered / (int) $row->total) * 100, 1) : 0,
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
