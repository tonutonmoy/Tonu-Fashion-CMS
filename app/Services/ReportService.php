<?php

namespace App\Services;

use App\Enums\ExpenseCategory;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
        private InventoryService $inventory,
    ) {}

    public function resolveDateRange(?string $preset, ?string $start = null, ?string $end = null): array
    {
        $now = now();

        return match ($preset) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                Carbon::parse($start ?? $now)->startOfDay(),
                Carbon::parse($end ?? $now)->endOfDay(),
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    public function getProfitLoss(Carbon $start, Carbon $end): array
    {
        $revenue = $this->revenueBetween($start, $end);
        $cogs = $this->cogsBetween($start, $end);
        $expenseTotal = $this->expenses->sumBetween($start, $end);
        $courierCost = $this->expenses->sumByCategoryBetween(ExpenseCategory::Courier->value, $start, $end);
        $otherExpenses = max(0, $expenseTotal - $courierCost);
        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenseTotal;

        $revenueChart = $this->revenueChartBetween($start, $end);
        $expenseChart = $this->expenses->chartTotalsBetween($start, $end);

        $periods = $revenueChart->pluck('period')
            ->merge($expenseChart->pluck('period'))
            ->unique()
            ->sort()
            ->values();

        $chart = [
            'labels' => $periods->all(),
            'revenue' => $periods->map(fn ($p) => (float) ($revenueChart->firstWhere('period', $p)['total'] ?? 0))->all(),
            'expenses' => $periods->map(fn ($p) => (float) ($expenseChart->firstWhere('period', $p)['total'] ?? 0))->all(),
        ];

        return [
            'revenue' => round($revenue, 2),
            'cogs' => round($cogs, 2),
            'expenses' => round($expenseTotal, 2),
            'courier_cost' => round($courierCost, 2),
            'other_expenses' => round($otherExpenses, 2),
            'gross_profit' => round($grossProfit, 2),
            'net_profit' => round($netProfit, 2),
            'pending_orders' => Order::query()->where('status', OrderStatus::Pending)->count(),
            'payment_orders' => $this->countPaidBetween($start, $end),
            'status_counts' => $this->statusCounts(),
            'chart' => $chart,
            'start' => $start,
            'end' => $end,
        ];
    }

    public function getVariantSalesReport(Carbon $start, Carbon $end): Collection
    {
        $items = OrderItem::query()
            ->whereHas('order', function ($query) use ($start, $end) {
                $query->where('status', OrderStatus::Payment)
                    ->whereBetween('payment_at', [$start, $end]);
            })
            ->get(['product_id', 'product_variant_id', 'product_name', 'size', 'color', 'quantity']);

        $salesByKey = $items->groupBy(fn ($item) => $item->product_variant_id ?: 'product:'.$item->product_id)
            ->map(fn (Collection $group) => $group->sum('quantity'));

        return $this->inventory->cachedVariantRows()->map(function (array $row) use ($salesByKey) {
            $key = $row['is_variant'] ? $row['id'] : 'product:'.$row['product_id'];

            return [
                'product_id' => $row['product_id'],
                'product_variant_id' => $row['is_variant'] ? $row['id'] : null,
                'product_name' => $row['product_name'],
                'variant_label' => $row['variant_label'],
                'units_sold' => (int) ($salesByKey[$key] ?? 0),
                'stock' => $row['stock'],
                'reserved_stock' => $row['reserved_stock'],
                'available_stock' => $row['available_stock'],
                'purchase_price' => $row['purchase_price'],
                'stock_value' => $row['stock_value'],
            ];
        })->sortByDesc('units_sold')->values();
    }

    public function calculateOrderCogs(Order $order): float
    {
        $order->loadMissing(['items.product', 'items.variant']);

        $total = 0.0;

        foreach ($order->items as $item) {
            $purchasePrice = 0.0;

            if ($item->product_variant_id && $item->variant) {
                $purchasePrice = (float) ($item->product?->purchase_price ?? 0);
            } elseif ($item->product) {
                $purchasePrice = (float) ($item->product->purchase_price ?? 0);
            }

            $total += $purchasePrice * (int) $item->quantity;
        }

        return round($total, 2);
    }

    public function revenueBetween(Carbon $start, Carbon $end): float
    {
        return (float) Order::query()
            ->where('status', OrderStatus::Payment)
            ->whereBetween('payment_at', [$start, $end])
            ->sum('total');
    }

    public function cogsBetween(Carbon $start, Carbon $end): float
    {
        return (float) Order::query()
            ->where('status', OrderStatus::Payment)
            ->whereBetween('payment_at', [$start, $end])
            ->sum('cogs');
    }

    private function countPaidBetween(Carbon $start, Carbon $end): int
    {
        return Order::query()
            ->where('status', OrderStatus::Payment)
            ->whereBetween('payment_at', [$start, $end])
            ->count();
    }

    private function statusCounts(): array
    {
        return collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status) => [
                $status->value => Order::query()->where('status', $status)->count(),
            ])
            ->all();
    }

    private function revenueChartBetween(Carbon $start, Carbon $end): Collection
    {
        return Order::query()
            ->where('status', OrderStatus::Payment)
            ->whereBetween('payment_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(payment_at, '%Y-%m') as period, SUM(total) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn ($row) => [
                'period' => $row->period,
                'total' => (float) $row->total,
            ]);
    }
}
