@extends('layouts.admin')
@section('title', 'Inventory Details')
@section('content')
@php
    $presets = [
        'today' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'year' => 'This Year',
        'custom' => 'Custom',
    ];
    $exportQuery = http_build_query(array_filter([
        'range' => $preset,
        'start_date' => $preset === 'custom' ? $startDate : null,
        'end_date' => $preset === 'custom' ? $endDate : null,
    ]));
@endphp

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Inventory & Sales Report</h2>
        <p class="text-sm text-gray-500">Variant-wise sales for {{ $startDate }} — {{ $endDate }}</p>
    </div>
    <a href="{{ route('admin.reports.export') }}?{{ $exportQuery }}" class="btn-primary inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download Excel (CSV)
    </a>
</div>

<div class="card p-5 mb-6">
    <p class="text-sm text-gray-500">Total Stock Value (current warehouse)</p>
    <p class="text-3xl font-bold text-brand-600 mt-1">{{ format_bdt($totalStockValue) }}</p>
</div>

<form method="GET" class="card p-4 mb-4 flex flex-col gap-4" id="inventory-report-filter">
    <div class="flex flex-wrap gap-2">
        @foreach($presets as $value => $label)
        <label class="inline-flex">
            <input type="radio" name="range" value="{{ $value }}" class="sr-only peer" @checked($preset === $value)>
            <span class="px-3 py-1.5 rounded-lg border text-sm cursor-pointer peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 border-gray-200 hover:bg-gray-50">{{ $label }}</span>
        </label>
        @endforeach
    </div>
    <div id="inventory-custom-dates" class="flex flex-col sm:flex-row gap-3 {{ $preset === 'custom' ? '' : 'hidden' }}">
        <div>
            <label class="label" for="start_date">From</label>
            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="input">
        </div>
        <div>
            <label class="label" for="end_date">To</label>
            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="input">
        </div>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn-primary">Apply</button>
        <a href="{{ route('admin.reports.inventory-details') }}" class="btn-secondary">Reset</a>
    </div>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">Variant</th>
                    <th class="px-4 py-3 text-right">Units Sold</th>
                    <th class="px-4 py-3 text-right">On Hand</th>
                    <th class="px-4 py-3 text-right">Reserved</th>
                    <th class="px-4 py-3 text-right">Available</th>
                    <th class="px-4 py-3 text-right">Purchase Price</th>
                    <th class="px-4 py-3 text-right">Stock Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $row)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $row['product_name'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['variant_label'] }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ $row['units_sold'] }}</td>
                    <td class="px-4 py-3 text-right">{{ $row['stock'] }}</td>
                    <td class="px-4 py-3 text-right text-blue-600">{{ $row['reserved_stock'] }}</td>
                    <td class="px-4 py-3 text-right">{{ $row['available_stock'] }}</td>
                    <td class="px-4 py-3 text-right">{{ format_bdt($row['purchase_price']) }}</td>
                    <td class="px-4 py-3 text-right">{{ format_bdt($row['stock_value']) }}</td>
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
@endsection

@push('scripts')
<script>
document.querySelectorAll('#inventory-report-filter input[name="range"]').forEach((input) => {
    input.addEventListener('change', () => {
        const custom = document.getElementById('inventory-custom-dates');
        custom.classList.toggle('hidden', input.value !== 'custom');
        if (input.value !== 'custom') {
            document.getElementById('inventory-report-filter').submit();
        }
    });
});
</script>
@endpush
