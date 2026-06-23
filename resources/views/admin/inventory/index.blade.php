@extends('layouts.admin')
@section('title', 'Inventory')
@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Inventory Overview</h2>
        <p class="text-sm text-gray-500">Product totals include all variants. Available = stock − reserved.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.inventory.log') }}" class="btn-secondary">Movement Log</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-sm text-gray-500">Total Stock Value (purchase cost)</p>
        <p class="text-3xl font-bold text-brand-600 mt-1">{{ format_bdt($totalStockValue) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Low stock items (&lt; {{ $threshold }})</p>
        <p class="text-3xl font-bold text-orange-600 mt-1">{{ $groups->filter(fn ($g) => $g['available_stock'] < $threshold)->count() }}</p>
    </div>
    <div class="card p-5">
        <form action="{{ route('admin.inventory.preferences') }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
            @csrf
            <div class="flex-1 w-full">
                <label class="label" for="low_stock_threshold">Low stock alert threshold</label>
                <input type="number" min="1" max="1000" name="low_stock_threshold" id="low_stock_threshold" value="{{ $threshold }}" class="input">
            </div>
            <button type="submit" class="btn-primary">Save</button>
        </form>
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
                    <th class="px-4 py-3 text-left w-8"></th>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-right">On Hand</th>
                    <th class="px-4 py-3 text-right">Reserved</th>
                    <th class="px-4 py-3 text-right">Available</th>
                    <th class="px-4 py-3 text-right">Value</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($groups as $group)
                @php $low = $group['available_stock'] < $threshold; @endphp
                <tr class="{{ $low ? 'bg-orange-50/60' : '' }}">
                    <td class="px-4 py-3">
                        @if($group['has_variants'])
                        <button type="button" class="text-gray-500 hover:text-gray-800 js-inv-toggle" data-target="inv-{{ $group['product_id'] }}" aria-label="Toggle variants">▾</button>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $group['product_name'] }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $group['sku'] }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ $group['stock'] }}</td>
                    <td class="px-4 py-3 text-right text-blue-600">{{ $group['reserved_stock'] }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $low ? 'text-orange-600' : '' }}">{{ $group['available_stock'] }}</td>
                    <td class="px-4 py-3 text-right">{{ format_bdt($group['stock_value']) }}</td>
                    <td class="px-4 py-3 text-right">
                        @unless($group['has_variants'])
                        <button type="button"
                                class="btn-secondary text-xs js-inventory-adjust"
                                data-product-id="{{ $group['product_id'] }}"
                                data-label="{{ $group['product_name'] }}"
                                data-available="{{ $group['available_stock'] }}">
                            Adjust
                        </button>
                        @endunless
                    </td>
                </tr>
                @if($group['has_variants'])
                @foreach($group['variants'] as $variant)
                <tr class="hidden js-inv-child inv-{{ $group['product_id'] }} bg-gray-50/80">
                    <td></td>
                    <td class="px-4 py-3 pl-8 text-gray-600">{{ $variant['variant_label'] }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $variant['sku'] }}</td>
                    <td class="px-4 py-3 text-right">{{ $variant['stock'] }}</td>
                    <td class="px-4 py-3 text-right text-blue-600">{{ $variant['reserved_stock'] }}</td>
                    <td class="px-4 py-3 text-right">{{ $variant['available_stock'] }}</td>
                    <td class="px-4 py-3 text-right">{{ format_bdt($variant['stock_value']) }}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button"
                                class="btn-secondary text-xs js-inventory-adjust"
                                data-variant-id="{{ $variant['is_variant'] ? $variant['id'] : '' }}"
                                data-product-id="{{ $variant['is_variant'] ? '' : $variant['product_id'] }}"
                                data-label="{{ $variant['product_name'] }} — {{ $variant['variant_label'] }}"
                                data-available="{{ $variant['available_stock'] }}">
                            Adjust
                        </button>
                    </td>
                </tr>
                @endforeach
                @endif
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
    <div class="relative z-10 flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-1">Adjust Stock</h3>
            <p class="text-sm text-gray-500 mb-4" id="inventory-adjust-label"></p>
            <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="variant_id" id="inventory-adjust-variant-id">
                <input type="hidden" name="product_id" id="inventory-adjust-product-id">
                <div>
                    <label class="label">Quantity change (+ add / − remove)</label>
                    <input type="number" name="quantity" id="inventory-adjust-qty" class="input" required>
                    <p class="text-xs text-gray-500 mt-1">Available now: <span id="inventory-adjust-available">0</span></p>
                </div>
                <div>
                    <label class="label">Note</label>
                    <input type="text" name="note" class="input" required placeholder="Reason for adjustment">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-secondary" data-inventory-modal-close>Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-inv-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
        const rows = document.querySelectorAll('.js-inv-child.' + btn.dataset.target);
        const open = rows.length && rows[0].classList.contains('hidden');
        rows.forEach((row) => row.classList.toggle('hidden', !open));
        btn.textContent = open ? '▴' : '▾';
    });
});

const modal = document.getElementById('inventory-adjust-modal');
document.querySelectorAll('.js-inventory-adjust').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.getElementById('inventory-adjust-variant-id').value = btn.dataset.variantId || '';
        document.getElementById('inventory-adjust-product-id').value = btn.dataset.productId || '';
        document.getElementById('inventory-adjust-label').textContent = btn.dataset.label || '';
        document.getElementById('inventory-adjust-available').textContent = btn.dataset.available || '0';
        document.getElementById('inventory-adjust-qty').value = '';
        modal.classList.remove('hidden');
    });
});
modal?.querySelectorAll('[data-inventory-modal-close]').forEach((el) => {
    el.addEventListener('click', () => modal.classList.add('hidden'));
});
</script>
@endpush
