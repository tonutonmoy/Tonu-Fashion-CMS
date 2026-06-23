@extends('layouts.admin')
@section('title', 'Inventory')
@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Inventory Overview</h2>
        <p class="text-sm text-gray-500">Available = stock − reserved (pending orders)</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.inventory.log') }}" class="btn-secondary">Movement Log</a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-sm text-gray-500">Total Stock Value (purchase cost)</p>
        <p class="text-3xl font-bold text-brand-600 mt-1">{{ format_bdt($totalStockValue) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Low stock items (&lt; {{ $threshold }})</p>
        <p class="text-3xl font-bold text-orange-600 mt-1">{{ $rows->where('available_stock', '<', $threshold)->count() }}</p>
    </div>
</div>

<form method="GET" class="card p-4 mb-4 flex flex-col sm:flex-row gap-3 flex-wrap items-center" data-admin-auto-filter>
    <input type="text" name="search" value="{{ request('search') }}" class="input flex-1 min-w-[12rem]" placeholder="Search product or SKU…">
    <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="low_stock" value="1" class="rounded" @checked($lowStockOnly)>
        Low stock only (&lt; {{ $threshold }})
    </label>
    <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">Reset</a>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">Variant</th>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-right">On Hand</th>
                    <th class="px-4 py-3 text-right">Reserved</th>
                    <th class="px-4 py-3 text-right">Available</th>
                    <th class="px-4 py-3 text-right">Value</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $row)
                <tr class="{{ $row['available_stock'] < $threshold ? 'bg-orange-50/60' : '' }}">
                    <td class="px-4 py-3 font-medium">{{ $row['product_name'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['variant_label'] }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $row['sku'] }}</td>
                    <td class="px-4 py-3 text-right">{{ $row['stock'] }}</td>
                    <td class="px-4 py-3 text-right text-blue-600">{{ $row['reserved_stock'] }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $row['available_stock'] < $threshold ? 'text-orange-600' : '' }}">{{ $row['available_stock'] }}</td>
                    <td class="px-4 py-3 text-right">{{ format_bdt($row['stock_value']) }}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button"
                                class="btn-secondary text-xs js-inventory-adjust"
                                data-variant-id="{{ $row['is_variant'] ? $row['id'] : '' }}"
                                data-product-id="{{ $row['is_variant'] ? '' : $row['product_id'] }}"
                                data-label="{{ $row['product_name'] }} — {{ $row['variant_label'] }}"
                                data-available="{{ $row['available_stock'] }}">
                            Adjust
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No inventory rows found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="inventory-adjust-modal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" data-inventory-modal-close></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md p-6 relative">
            <h3 class="text-lg font-semibold mb-1">Adjust Stock</h3>
            <p class="text-sm text-gray-500 mb-4" id="inventory-adjust-label"></p>
            <p class="text-xs text-gray-400 mb-4">Available now: <span id="inventory-adjust-available"></span></p>
            <form id="inventory-adjust-form" class="space-y-4">
                @csrf
                <input type="hidden" name="variant_id" id="inventory-variant-id">
                <input type="hidden" name="product_id" id="inventory-product-id">
                <div>
                    <label class="label">Change quantity</label>
                    <p class="text-xs text-gray-500 mb-2">Use +10 to add, −5 to remove</p>
                    <input type="number" name="quantity" id="inventory-quantity" class="input" required placeholder="e.g. 10 or -3">
                </div>
                <div>
                    <label class="label">Note</label>
                    <textarea name="note" id="inventory-note" class="input" rows="3" required placeholder="Reason for adjustment…"></textarea>
                </div>
                <p id="inventory-adjust-error" class="text-sm text-red-600 hidden"></p>
                <div class="flex gap-2 justify-end">
                    <button type="button" class="btn-secondary" data-inventory-modal-close>Cancel</button>
                    <button type="submit" class="btn-primary">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('inventory-adjust-modal');
    const form = document.getElementById('inventory-adjust-form');
    const errorEl = document.getElementById('inventory-adjust-error');

    const openModal = (btn) => {
        document.getElementById('inventory-adjust-label').textContent = btn.dataset.label;
        document.getElementById('inventory-adjust-available').textContent = btn.dataset.available;
        document.getElementById('inventory-variant-id').value = btn.dataset.variantId || '';
        document.getElementById('inventory-product-id').value = btn.dataset.productId || '';
        document.getElementById('inventory-quantity').value = '';
        document.getElementById('inventory-note').value = '';
        errorEl.classList.add('hidden');
        modal.classList.remove('hidden');
    };

    const closeModal = () => modal.classList.add('hidden');

    document.querySelectorAll('.js-inventory-adjust').forEach((btn) => {
        btn.addEventListener('click', () => openModal(btn));
    });

    modal.querySelectorAll('[data-inventory-modal-close]').forEach((el) => {
        el.addEventListener('click', closeModal);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorEl.classList.add('hidden');

        const body = new FormData(form);
        try {
            const response = await fetch(@json(route('admin.inventory.adjust')), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body,
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                errorEl.textContent = data.message || 'Could not adjust stock.';
                errorEl.classList.remove('hidden');
                return;
            }
            window.location.reload();
        } catch {
            errorEl.textContent = 'Network error. Please try again.';
            errorEl.classList.remove('hidden');
        }
    });
});
</script>
@endpush
