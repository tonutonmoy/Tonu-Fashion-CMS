@extends('layouts.frontend')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>
    <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="card p-6 space-y-4">
            <h2 class="font-semibold">Shipping Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="label">Full Name *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Phone *</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" class="input" required placeholder="01XXXXXXXXX">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" class="input">
                </div>
                <div>
                    <label class="label">Division *</label>
                    <input type="text" name="shipping_division" value="{{ old('shipping_division') }}" class="input" required placeholder="Dhaka">
                </div>
                <div>
                    <label class="label">District *</label>
                    <input type="text" name="shipping_district" value="{{ old('shipping_district') }}" class="input" required>
                </div>
                <div>
                    <label class="label">Upazila</label>
                    <input type="text" name="shipping_upazila" value="{{ old('shipping_upazila') }}" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Full Address *</label>
                    <textarea name="shipping_address" rows="3" class="input" required>{{ old('shipping_address') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Order Note</label>
                    <textarea name="order_note" rows="2" class="input" placeholder="Special instructions...">{{ old('order_note') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Coupon Code</label>
                    <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" class="input" placeholder="SAVE10">
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="font-semibold mb-4">Order Summary</h2>
            @foreach($items as $item)
                <div class="flex justify-between text-sm py-2 border-b border-gray-100">
                    <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <span>{{ format_bdt($item->line_total) }}</span>
                </div>
            @endforeach
            <div class="flex justify-between py-2 text-sm"><span>Subtotal</span><span>{{ format_bdt($subtotal) }}</span></div>
            <div class="flex justify-between py-2 text-sm"><span>Shipping</span><span>{{ format_bdt($shipping) }}</span></div>
            <div class="flex justify-between py-2 font-bold text-lg border-t mt-2 pt-2">
                <span>Total</span>
                <span>{{ format_bdt($subtotal + $shipping) }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-4">Payment: Cash on Delivery (COD)</p>
            <button type="submit" class="btn-primary w-full mt-4">Place Order</button>
        </div>
    </form>
</div>
@endsection
