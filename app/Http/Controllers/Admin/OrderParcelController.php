<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourierType;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\ParcelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderParcelController extends Controller
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private ParcelService $parcels,
    ) {}

    public function createParcel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->orders->findByOrderNumber($orderNumber);

        $request->validate([
            'courier' => ['nullable', 'string', 'in:'.implode(',', array_column(\App\Enums\CourierType::cases(), 'value'))],
        ]);

        $courier = $request->input('courier')
            ? CourierType::from($request->input('courier'))
            : null;

        try {
            $this->parcels->createParcel($order, $courier);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Courier parcel created. Order status updated to Courier.');
    }

    public function syncParcel(string $orderNumber): RedirectResponse
    {
        $order = $this->orders->findByOrderNumber($orderNumber);
        $parcel = $order->courierParcel;

        if (! $parcel) {
            return back()->with('error', 'No courier parcel found for this order.');
        }

        try {
            $this->parcels->syncParcel($parcel);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Parcel status synced.');
    }

    public function invoice(string $orderNumber, string $format = 'a4'): View
    {
        $order = $this->orders->findByOrderNumber($orderNumber);
        $view = $format === 'thermal' ? 'admin.orders.print.invoice-thermal' : 'admin.orders.print.invoice-a4';

        return view($view, [
            'order' => $order,
            'store' => [
                'name' => setting('name', config('app.name')),
                'phone' => setting('phone'),
                'address' => setting('address'),
            ],
        ]);
    }

    public function packingSlip(string $orderNumber): View
    {
        $order = $this->orders->findByOrderNumber($orderNumber);

        return view('admin.orders.print.packing-slip', [
            'order' => $order,
            'store' => [
                'name' => setting('name', config('app.name')),
                'phone' => setting('phone'),
            ],
            'trackUrl' => route('orders.show', $order->order_number),
        ]);
    }

    public function label(string $orderNumber): View|RedirectResponse
    {
        $order = $this->orders->findByOrderNumber($orderNumber);
        $parcel = $order->courierParcel;

        if ($parcel?->tracking_url) {
            return redirect()->away($parcel->tracking_url);
        }

        return view('admin.orders.print.label', [
            'order' => $order,
            'parcel' => $parcel,
            'store' => ['name' => setting('name', config('app.name'))],
        ]);
    }
}
