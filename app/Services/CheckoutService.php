<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        private CartService $cart,
        private OrderRepositoryInterface $orders,
        private CouponRepositoryInterface $coupons,
        private ShippingService $shipping,
        private MarketingEventService $marketingEvents,
        private PaymentSettingsService $paymentSettings
    ) {}

    public function placeOrder(array $data): Order
    {
        $items = $this->cart->getItems();

        if ($items->isEmpty()) {
            throw new \RuntimeException('Your cart is empty.');
        }

        if (Auth::check() && ! Auth::user()->canPlaceOrders()) {
            throw new \RuntimeException('You are not allowed to place orders. Contact support.');
        }

        return DB::transaction(function () use ($data, $items) {
            $subtotal = $this->cart->subtotal();
            $coupon = null;
            $discount = 0;

            if (! empty($data['coupon_code'])) {
                $coupon = $this->coupons->findByCode($data['coupon_code']);
                if ($coupon && $coupon->isValid()) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }

            if ($this->shipping->cartRequiresShippingSelection($items, $subtotal) && empty($data['delivery_zone'])) {
                throw new \RuntimeException('Please select a delivery method.');
            }

            $shipping = $this->shipping->calculateForCart(
                $items,
                $subtotal,
                $data['delivery_zone'] ?? null
            );

            $location = $this->shipping->locationFromZone($data['delivery_zone'] ?? null);
            $total = max(0, $subtotal - $discount + $shipping);
            $eventId = $data['purchase_event_id'] ?? $this->marketingEvents->generateEventId();
            $paymentMethod = PaymentMethod::from($data['payment_method'] ?? PaymentMethod::CashOnDelivery->value);

            if (! $this->paymentSettings->isMethodEnabled($paymentMethod)) {
                throw new \RuntimeException('Selected payment method is not available.');
            }

            $order = $this->orders->create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'status' => OrderStatus::Pending,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod->isOnline() ? PaymentStatus::Pending->value : PaymentStatus::Pending->value,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shipping,
                'total' => $total,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? Auth::user()?->email,
                'shipping_division' => $location['division'],
                'shipping_district' => $location['district'],
                'shipping_upazila' => null,
                'shipping_area' => null,
                'shipping_address' => $data['shipping_address'],
                'order_note' => null,
                'purchase_event_id' => $eventId,
                'fbp' => $data['fbp'] ?? request()->cookie('_fbp'),
                'fbc' => $data['fbc'] ?? request()->cookie('_fbc'),
            ]);

            foreach ($items as $item) {
                $unitPrice = $item->variant
                    ? $item->variant->price
                    : $item->product->effective_price;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->variant?->sku ?? $item->product->sku,
                    'size' => $item->variant?->size,
                    'color' => $item->variant?->color,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $item->quantity,
                ]);

                $this->decrementStock($item->product_id, $item->product_variant_id, $item->quantity);
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $this->cart->clear();

            $order = $order->load('items');

            if (! $paymentMethod->isOnline()) {
                $this->marketingEvents->trackPurchase($order);
            }

            return $order;
        });
    }

    public function applyCoupon(string $code): array
    {
        $coupon = $this->coupons->findByCode($code);

        if (! $coupon || ! $coupon->isValid()) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Invalid or expired coupon.'];
        }

        $subtotal = $this->cart->subtotal();
        $discount = $coupon->calculateDiscount($subtotal);

        if ($discount <= 0) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Minimum order amount not met.'];
        }

        return [
            'valid' => true,
            'discount' => $discount,
            'code' => $coupon->code,
            'message' => 'Coupon applied successfully.',
        ];
    }

    public function previewTotals(?string $deliveryZone = null): array
    {
        $items = $this->cart->getItems();
        $subtotal = $this->cart->subtotal();
        $requiresSelection = $this->shipping->cartRequiresShippingSelection($items, $subtotal);
        $shipping = $this->shipping->calculateForCart($items, $subtotal, $deliveryZone);

        return [
            'subtotal' => $subtotal,
            'subtotal_label' => format_bdt($subtotal),
            'discount' => 0,
            'shipping' => $shipping,
            'shipping_label' => $requiresSelection
                ? $this->shipping->zoneLabel($deliveryZone)
                : $this->shipping->zoneLabel(null),
            'free_delivery_reason' => $this->shipping->freeDeliveryReason($items, $subtotal),
            'requires_delivery_selection' => $requiresSelection,
            'delivery_options' => $this->shipping->deliveryOptions($items, $subtotal),
            'total' => max(0, $subtotal + $shipping),
            'count' => $this->cart->count(),
            'empty' => $items->isEmpty(),
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'line_total' => $item->line_total,
                'line_total_label' => format_bdt($item->line_total),
                'name' => $item->product->name,
                'variant' => $item->variant?->display_name,
                'image' => $item->product->primary_image ? image_url($item->product->primary_image) : null,
                'free_delivery' => (bool) $item->product->free_delivery,
            ])->values()->all(),
        ];
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.strtoupper(Str::random(8));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }

    private function decrementStock(int $productId, ?int $variantId, int $quantity): void
    {
        if ($variantId) {
            ProductVariant::query()->whereKey($variantId)->decrement('stock', $quantity);
        } else {
            \App\Models\Product::query()->whereKey($productId)->decrement('stock', $quantity);
        }
    }
}
