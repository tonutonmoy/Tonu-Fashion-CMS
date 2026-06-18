<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentCallbackController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function callback(Request $request, string $gateway): RedirectResponse|View
    {
        $method = PaymentMethod::from($gateway);

        try {
            $order = $this->payments->handleCallback($method, $request->all());
        } catch (\Throwable $e) {
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }

        if ($order->payment_status === PaymentStatus::Paid) {
            return redirect()->route('orders.show', $order)
                ->with('success', 'Payment successful! Your order is confirmed.')
                ->with('track_purchase', true);
        }

        return redirect()->route('orders.show', $order)
            ->with('error', 'Payment was not completed. You can try again from your order.');
    }

    public function ipn(Request $request, string $gateway): \Illuminate\Http\Response
    {
        try {
            $this->payments->handleCallback(PaymentMethod::from($gateway), $request->all());
        } catch (\Throwable) {
            //
        }

        return response('OK');
    }
}
