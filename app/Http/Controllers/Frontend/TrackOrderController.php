<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackOrderRequest;
use App\Services\ParcelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrackOrderController extends Controller
{
    public function __construct(private ParcelService $parcels) {}

    public function show(): View
    {
        return view('frontend.track-order', [
            'order' => null,
        ]);
    }

    public function track(TrackOrderRequest $request): View|RedirectResponse
    {
        $order = $this->parcels->findTrackableOrder(
            $request->customer_phone,
            $request->order_number
        );

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', 'No order found with that phone number and order ID.');
        }

        return view('frontend.track-order', [
            'order' => $order,
        ]);
    }
}
