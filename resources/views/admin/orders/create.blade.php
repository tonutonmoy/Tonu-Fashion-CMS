@extends('layouts.admin')
@section('title', 'Create Custom Order')
@section('content')
<h2 class="text-xl font-semibold mb-6">Create Custom Order</h2>

<form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-6" id="admin-order-form">
    @csrf

    <div class="card p-6 space-y-4">
        <h3 class="font-semibold">Customer</h3>
        <div>
            <label class="label">Link to registered customer (optional)</label>
            <select name="user_id" id="order-user-id" class="input w-full">
                <option value="">Guest / manual entry</option>
                @foreach($customers as $customer)
                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-email="{{ $customer->email }}" data-phone="{{ $customer->phone }}">{{ $customer->name }} ({{ $customer->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="label">Customer Name</label><input name="customer_name" id="customer-name" class="input w-full" value="{{ old('customer_name') }}" required></div>
            <div><label class="label">Phone</label><input name="customer_phone" id="customer-phone" class="input w-full" value="{{ old('customer_phone') }}" required></div>
            <div class="md:col-span-2"><label class="label">Email</label><input type="email" name="customer_email" id="customer-email" class="input w-full" value="{{ old('customer_email') }}"></div>
        </div>
    </div>

    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">Order Items</h3>
            <button type="button" id="add-order-item" class="btn-secondary text-sm">+ Add Product</button>
        </div>
        <div id="order-items" class="space-y-3">
            <div class="order-item-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end border-b border-gray-100 pb-3">
                <div class="md:col-span-5">
                    <label class="label">Product</label>
                    <select name="items[0][product_id]" class="input w-full product-select" required>
                        <option value="">Select product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->effective_price }}" data-variants="{{ $product->variants->count() }}">{{ $product->name }} — {{ format_bdt($product->effective_price) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="label">Variant</label>
                    <select name="items[0][product_variant_id]" class="input w-full variant-select">
                        <option value="">Default</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Qty</label>
                    <input type="number" name="items[0][quantity]" class="input w-full" value="1" min="1" required>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Unit Price</label>
                    <input type="number" step="0.01" name="items[0][unit_price]" class="input w-full unit-price" placeholder="Auto">
                </div>
            </div>
        </div>
    </div>

    <div class="card p-6 space-y-4">
        <h3 class="font-semibold">Shipping & Payment</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">Division</label>
                <select name="shipping_division" class="input w-full" required>
                    @foreach($divisions as $division)
                    <option value="{{ $division }}" @selected(old('shipping_division') === $division)>{{ $division }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="label">District</label><input name="shipping_district" class="input w-full" value="{{ old('shipping_district', 'Dhaka') }}" required></div>
            <div><label class="label">Area</label><input name="shipping_area" class="input w-full" value="{{ old('shipping_area') }}"></div>
            <div><label class="label">Shipping Cost (optional)</label><input type="number" step="0.01" name="shipping_cost" class="input w-full" value="{{ old('shipping_cost') }}" placeholder="Auto calculate"></div>
            <div class="md:col-span-2"><label class="label">Address</label><textarea name="shipping_address" class="input w-full" rows="2" required>{{ old('shipping_address') }}</textarea></div>
            <div>
                <label class="label">Payment Method</label>
                <select name="payment_method" class="input w-full" required>
                    @foreach($paymentMethods as $method)
                    <option value="{{ $method->value }}" @selected(old('payment_method', 'cash_on_delivery') === $method->value)>{{ $method->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Order Status</label>
                <select name="status" class="input w-full" required>
                    @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status', 'pending') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2"><label class="label">Order Note</label><textarea name="order_note" class="input w-full" rows="2">{{ old('order_note') }}</textarea></div>
            <div><label class="label">Coupon Code</label><input name="coupon_code" class="input w-full" value="{{ old('coupon_code') }}"></div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-primary">Create Order</button>
        <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>

@php
$productVariants = $products->mapWithKeys(fn ($p) => [
    $p->id => $p->variants->map(fn ($v) => ['id' => $v->id, 'label' => trim(($v->size ?? '').'/'.($v->color ?? ''), '/'), 'price' => $v->price])->values(),
])->toArray();
@endphp

<script>
document.addEventListener('DOMContentLoaded', () => {
    const variants = @json($productVariants);
    const itemsWrap = document.getElementById('order-items');
    let rowIndex = 1;

    document.getElementById('order-user-id')?.addEventListener('change', (e) => {
        const opt = e.target.selectedOptions[0];
        if (!opt || !opt.value) return;
        document.getElementById('customer-name').value = opt.dataset.name || '';
        document.getElementById('customer-email').value = opt.dataset.email || '';
        document.getElementById('customer-phone').value = opt.dataset.phone || '';
    });

    function bindProductSelect(row) {
        const productSelect = row.querySelector('.product-select');
        const variantSelect = row.querySelector('.variant-select');
        const priceInput = row.querySelector('.unit-price');

        productSelect.addEventListener('change', () => {
            const productId = productSelect.value;
            variantSelect.innerHTML = '<option value="">Default</option>';
            (variants[productId] || []).forEach((v) => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = v.label + ' — ৳' + v.price;
                opt.dataset.price = v.price;
                variantSelect.appendChild(opt);
            });
            const selected = productSelect.selectedOptions[0];
            if (selected?.dataset.price && !priceInput.value) {
                priceInput.placeholder = selected.dataset.price;
            }
        });

        variantSelect.addEventListener('change', () => {
            const selected = variantSelect.selectedOptions[0];
            if (selected?.dataset.price) {
                priceInput.placeholder = selected.dataset.price;
            }
        });
    }

    itemsWrap.querySelectorAll('.order-item-row').forEach(bindProductSelect);

    document.getElementById('add-order-item')?.addEventListener('click', () => {
        const first = itemsWrap.querySelector('.order-item-row');
        const clone = first.cloneNode(true);
        clone.querySelectorAll('input').forEach((el) => {
            if (el.name.includes('[quantity]')) el.value = '1';
            else el.value = '';
        });
        clone.querySelectorAll('select').forEach((el) => {
            el.name = el.name.replace(/\[\d+\]/, `[${rowIndex}]`);
            if (el.classList.contains('variant-select')) {
                el.innerHTML = '<option value="">Default</option>';
            } else {
                el.selectedIndex = 0;
            }
        });
        rowIndex++;
        itemsWrap.appendChild(clone);
        bindProductSelect(clone);
    });
});
</script>
@endsection
