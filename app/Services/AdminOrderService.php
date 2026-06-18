<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RecordStatus;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminOrderService
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private CouponRepositoryInterface $coupons,
        private ShippingService $shipping,
    ) {}

    public function createManualOrder(array $data): Order
    {
        $items = collect($data['items'] ?? [])->filter(fn ($item) => ! empty($item['product_id']) && (int) ($item['quantity'] ?? 0) > 0);

        if ($items->isEmpty()) {
            throw new \RuntimeException('Add at least one product to the order.');
        }

        return DB::transaction(function () use ($data, $items) {
            $lineItems = [];
            $subtotal = 0.0;

            foreach ($items as $item) {
                $product = Product::query()->with('variants')->findOrFail($item['product_id']);
                $variant = ! empty($item['product_variant_id'])
                    ? $product->variants->firstWhere('id', (int) $item['product_variant_id'])
                    : null;

                $quantity = (int) $item['quantity'];
                $unitPrice = isset($item['unit_price']) && $item['unit_price'] !== ''
                    ? (float) $item['unit_price']
                    : (float) ($variant?->price ?? $product->effective_price);

                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ];
            }

            $coupon = null;
            $discount = 0.0;

            if (! empty($data['coupon_code'])) {
                $coupon = $this->coupons->findByCode($data['coupon_code']);
                if ($coupon && $coupon->isValid()) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }

            $shipping = isset($data['shipping_cost']) && $data['shipping_cost'] !== ''
                ? (float) $data['shipping_cost']
                : $this->shipping->calculate($subtotal, $data['shipping_division'], $data['shipping_district']);

            $total = max(0, $subtotal - $discount + $shipping);
            $paymentMethod = PaymentMethod::from($data['payment_method'] ?? PaymentMethod::CashOnDelivery->value);
            $status = OrderStatus::from($data['status'] ?? OrderStatus::Confirmed->value);

            $order = $this->orders->create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $data['user_id'] ?? null,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'payment_status' => PaymentStatus::Pending->value,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shipping,
                'total' => $total,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'shipping_division' => $data['shipping_division'],
                'shipping_district' => $data['shipping_district'],
                'shipping_upazila' => $data['shipping_upazila'] ?? null,
                'shipping_area' => $data['shipping_area'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'order_note' => $data['order_note'] ?? null,
                'confirmed_at' => in_array($status, [OrderStatus::Confirmed, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::InTransit, OrderStatus::Delivered], true)
                    ? now()
                    : null,
            ]);

            foreach ($lineItems as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'product_variant_id' => $line['variant']?->id,
                    'product_name' => $line['product']->name,
                    'product_sku' => $line['variant']?->sku ?? $line['product']->sku,
                    'size' => $line['variant']?->size,
                    'color' => $line['variant']?->color,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total_price' => $line['total_price'],
                ]);

                $this->decrementStock($line['product']->id, $line['variant']?->id, $line['quantity']);
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order->load('items');
        });
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
            Product::query()->whereKey($productId)->decrement('stock', $quantity);
        }
    }
}
