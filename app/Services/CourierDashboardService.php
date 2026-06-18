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

        $parcels = CourierParcel::query()->get(['courier_name', 'current_status']);
        $totalParcels = $parcels->count();
        $deliveredParcels = $parcels->where('current_status', 'delivered')->count();
        $deliveryRate = $totalParcels > 0 ? round(($deliveredParcels / $totalParcels) * 100, 1) : 0;

        $courierPerformance = $parcels
            ->groupBy('courier_name')
            ->map(fn ($group, $courier) => [
                'courier' => $courier,
                'total' => $group->count(),
                'delivered' => $group->where('current_status', 'delivered')->count(),
                'returned' => $group->where('current_status', 'returned')->count(),
            ])
            ->values()
            ->map(function (array $row) {
                $row['rate'] = $row['total'] > 0
                    ? round(($row['delivered'] / $row['total']) * 100, 1)
                    : 0;

                return $row;
            });

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
