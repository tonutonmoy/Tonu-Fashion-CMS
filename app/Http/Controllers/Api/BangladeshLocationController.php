<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BangladeshLocationController extends Controller
{
    public function divisions(): JsonResponse
    {
        return response()->json(array_keys(config('bangladesh.divisions', [])));
    }

    public function districts(string $division): JsonResponse
    {
        $districts = config("bangladesh.divisions.{$division}", []);

        return response()->json($districts);
    }

    public function areas(Request $request): JsonResponse
    {
        $division = $request->get('division');
        $district = $request->get('district');
        $areas = config("bangladesh.areas.{$division}.{$district}", []);

        return response()->json($areas);
    }

    public function shippingQuote(Request $request, ShippingService $shipping): JsonResponse
    {
        $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'division' => 'required|string',
            'district' => 'nullable|string',
        ]);

        $subtotal = (float) $request->subtotal;
        $cost = $shipping->calculate($subtotal, $request->division, $request->district);

        return response()->json([
            'shipping_cost' => $cost,
            'label' => $shipping->label($request->division, $request->district),
            'free_shipping' => $cost === 0.0,
        ]);
    }
}
