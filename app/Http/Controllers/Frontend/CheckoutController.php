<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\MarketingEventService;
use App\Services\PaymentSettingsService;
use App\Services\ShippingService;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    use RendersThemeViews;

    public function __construct(
        private CartService $cart,
        private CheckoutService $checkout,
        private ShippingService $shipping,
        private MarketingEventService $marketingEvents,
        private PaymentSettingsService $paymentSettings,
        private PaymentService $payments,
    ) {}

    public function index(): View|RedirectResponse
    {
        if ($this->cart->getItems()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if (auth()->check() && ! auth()->user()->canPlaceOrders()) {
            return redirect()->route('cart.index')->with('error', 'You are not allowed to place orders. Contact support.');
        }

        $subtotal = $this->cart->subtotal();
        $items = $this->cart->getItems();
        $defaultZone = old('delivery_zone');
        if (! $defaultZone && $this->shipping->cartRequiresShippingSelection($items, $subtotal)) {
            $defaultZone = 'inside_dhaka';
        }
        $checkoutEventId = $this->marketingEvents->trackInitiateCheckout(
            $subtotal,
            $items->sum('quantity')
        )['event_id'] ?? $this->marketingEvents->generateEventId();

        return $this->themeView('checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shippingSettings' => $this->shipping->settings(),
            'checkoutEventId' => $checkoutEventId,
            'checkoutTotals' => $this->checkout->previewTotals($defaultZone),
            'paymentMethods' => $this->paymentSettings->enabledMethods(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        if (auth()->check() && ! auth()->user()->canPlaceOrders()) {
            return back()->with('error', 'You are not allowed to place orders. Contact support.')->withInput();
        }

        try {
            $order = $this->checkout->placeOrder($request->validated());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        if ($order->payment_method->isOnline()) {
            $result = $this->payments->initiateOnlinePayment($order);

            if (! $result->success || ! $result->redirectUrl) {
                return back()->with('error', $result->message ?? 'Could not start online payment.')->withInput();
            }

            return redirect()->away($result->redirectUrl);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully!')
            ->with('track_purchase', true);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);

        return response()->json($this->checkout->applyCoupon($request->code));
    }

    public function shippingQuote(Request $request): JsonResponse
    {
        $request->validate([
            'delivery_zone' => 'nullable|in:inside_dhaka,outside_dhaka',
        ]);

        return response()->json($this->checkout->previewTotals($request->delivery_zone));
    }
}
