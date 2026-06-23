<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseRequest;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Services\InventoryService;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reports,
        private InventoryService $inventory,
    ) {}

    public function profitLoss(Request $request): View
    {
        $preset = $request->get('range', 'month');
        [$start, $end] = $this->reports->resolveDateRange(
            $preset,
            $request->get('start_date'),
            $request->get('end_date'),
        );

        return view('admin.reports.profit_loss', [
            'report' => $this->reports->getProfitLoss($start, $end),
            'preset' => $preset,
            'startDate' => $request->get('start_date', $start->toDateString()),
            'endDate' => $request->get('end_date', $end->toDateString()),
        ]);
    }

    public function inventoryDetails(Request $request): View
    {
        $preset = $request->get('range', 'month');
        [$start, $end] = $this->reports->resolveDateRange(
            $preset,
            $request->get('start_date'),
            $request->get('end_date'),
        );

        $rows = $this->reports->getVariantSalesReport($start, $end);
        $inventoryRows = $this->inventory->variantRows();

        return view('admin.reports.inventory_details', [
            'rows' => $rows,
            'totalStockValue' => $inventoryRows->sum('stock_value'),
            'preset' => $preset,
            'startDate' => $request->get('start_date', $start->toDateString()),
            'endDate' => $request->get('end_date', $end->toDateString()),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $preset = $request->get('range', 'month');
        [$start, $end] = $this->reports->resolveDateRange(
            $preset,
            $request->get('start_date'),
            $request->get('end_date'),
        );

        $rows = $this->reports->getVariantSalesReport($start, $end);
        $filename = 'inventory-report-'.$start->format('Y-m-d').'-to-'.$end->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product', 'Variant', 'Units Sold', 'On Hand', 'Reserved', 'Available', 'Purchase Price', 'Stock Value']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['product_name'],
                    $row['variant_label'],
                    $row['units_sold'],
                    $row['stock'],
                    $row['reserved_stock'],
                    $row['available_stock'],
                    $row['purchase_price'],
                    $row['stock_value'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
