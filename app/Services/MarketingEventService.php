<?php

namespace App\Services;

use App\Jobs\SendFacebookConversionEventJob;
use App\Jobs\SendOrderSmsJob;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketingEventService
{
    public function __construct(
        private FacebookConversionApiService $facebookCapi
    ) {}

    public function generateEventId(): string
    {
        return (string) Str::uuid();
    }

    public function contextFromRequest(?Request $request = null): array
    {
        $request = $request ?? request();

        return [
            'event_id' => $this->generateEventId(),
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp') ?? session('marketing.fbp'),
            'fbc' => $request->cookie('_fbc') ?? session('marketing.fbc'),
            'email' => auth()->user()?->email,
            'phone' => auth()->user()?->phone,
        ];
    }

    public function trackViewContent(Product $product): array
    {
        $context = $this->contextFromRequest();
        SendFacebookConversionEventJob::dispatch('ViewContent', $context, [
            'content_ids' => [$product->sku],
            'content_name' => $product->name,
            'content_type' => 'product',
            'value' => (float) $product->effective_price,
            'currency' => 'BDT',
        ], $product->id);

        return $context;
    }

    public function trackAddToCart(Product $product, int $quantity, float $value): array
    {
        $context = $this->contextFromRequest();
        SendFacebookConversionEventJob::dispatch('AddToCart', $context, [
            'content_ids' => [$product->sku],
            'content_name' => $product->name,
            'content_type' => 'product',
            'value' => $value,
            'currency' => 'BDT',
            'num_items' => $quantity,
        ]);

        return $context;
    }

    public function trackInitiateCheckout(float $value, int $numItems): array
    {
        $context = $this->contextFromRequest();
        SendFacebookConversionEventJob::dispatch('InitiateCheckout', $context, [
            'value' => $value,
            'currency' => 'BDT',
            'num_items' => $numItems,
        ]);

        return $context;
    }

    public function trackPurchase(Order $order): void
    {
        $context = [
            'event_id' => $order->purchase_event_id,
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->userAgent(),
            'fbp' => $order->fbp,
            'fbc' => $order->fbc,
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
        ];

        SendFacebookConversionEventJob::dispatch('Purchase', $context, [
            'value' => (float) $order->total,
            'currency' => 'BDT',
            'content_ids' => $order->items->pluck('product_sku')->toArray(),
            'content_type' => 'product',
            'num_items' => $order->items->sum('quantity'),
            'order_id' => $order->order_number,
        ], null, $order->id);
    }
}
