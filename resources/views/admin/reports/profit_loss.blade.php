@extends('layouts.admin')
@section('title', 'Profit & Loss')
@section('content')
@php
    $presets = [
        'today' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'year' => 'This Year',
        'custom' => 'Custom',
    ];
    $statusLabels = collect(\App\Enums\OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]);
@endphp

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Profit & Loss</h2>
        <p class="text-sm text-gray-500">{{ $report['start']->format('d M Y') }} — {{ $report['end']->format('d M Y') }}</p>
        <p class="text-xs text-gray-400 mt-1">Revenue counts orders with <strong>Payment</strong> status in this period.</p>
    </div>
</div>

<form method="GET" class="card p-4 mb-6 flex flex-col gap-4" id="profit-loss-filter">
    <div class="flex flex-wrap gap-2">
        @foreach($presets as $value => $label)
        <label class="inline-flex">
            <input type="radio" name="range" value="{{ $value }}" class="sr-only peer" @checked($preset === $value)>
            <span class="px-3 py-1.5 rounded-lg border text-sm cursor-pointer peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 border-gray-200 hover:bg-gray-50">{{ $label }}</span>
        </label>
        @endforeach
    </div>
    <div id="custom-date-fields" class="flex flex-col sm:flex-row gap-3 {{ $preset === 'custom' ? '' : 'hidden' }}">
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
        <a href="{{ route('admin.reports.profit-loss') }}" class="btn-secondary">Reset</a>
    </div>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
    <div class="card p-5">
        <p class="text-sm text-gray-500">Revenue (Payment)</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ format_bdt($report['revenue']) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $report['payment_orders'] }} paid orders</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Product Cost (COGS)</p>
        <p class="text-3xl font-bold text-orange-600 mt-1">{{ format_bdt($report['cogs']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Courier Cost</p>
        <p class="text-3xl font-bold text-cyan-600 mt-1">{{ format_bdt($report['courier_cost']) }}</p>
        <p class="text-xs text-gray-400 mt-1">From courier expenses</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Other Expenses</p>
        <p class="text-3xl font-bold text-red-600 mt-1">{{ format_bdt($report['other_expenses']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-sm text-gray-500">Total Expenses</p>
        <p class="text-2xl font-bold text-red-700 mt-1">{{ format_bdt($report['expenses']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Gross Profit</p>
        <p class="text-2xl font-bold mt-1">{{ format_bdt($report['gross_profit']) }}</p>
        <p class="text-xs text-gray-400 mt-1">Revenue − COGS</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Net Profit</p>
        <p class="text-3xl font-bold {{ $report['net_profit'] >= 0 ? 'text-brand-600' : 'text-red-700' }} mt-1">{{ format_bdt($report['net_profit']) }}</p>
        <p class="text-xs text-gray-400 mt-1">After all expenses</p>
    </div>
    <div class="card p-5">
        <p class="text-sm text-gray-500">Pending Orders (now)</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $report['pending_orders'] }}</p>
    </div>
</div>

<div class="card p-5 mb-6">
    <h3 class="font-semibold mb-3">Order Status Counts (current)</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 text-sm">
        @foreach($report['status_counts'] as $status => $count)
        <div class="rounded-lg border border-gray-100 px-3 py-2">
            <p class="text-gray-500">{{ $statusLabels[$status] ?? $status }}</p>
            <p class="text-xl font-bold">{{ $count }}</p>
        </div>
        @endforeach
    </div>
</div>

<div class="card p-6">
    <h3 class="font-semibold mb-4">Revenue vs Expenses</h3>
    <div class="h-72">
        <canvas id="profit-loss-chart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.querySelectorAll('#profit-loss-filter input[name="range"]').forEach((input) => {
    input.addEventListener('change', () => {
        const custom = document.getElementById('custom-date-fields');
        custom.classList.toggle('hidden', input.value !== 'custom');
        if (input.value !== 'custom') {
            document.getElementById('profit-loss-filter').submit();
        }
    });
});

const chartData = @json($report['chart']);
const ctx = document.getElementById('profit-loss-chart');
if (ctx && chartData.labels.length) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                { label: 'Revenue', data: chartData.revenue, backgroundColor: 'rgba(34, 197, 94, 0.7)' },
                { label: 'Expenses', data: chartData.expenses, backgroundColor: 'rgba(239, 68, 68, 0.7)' },
            ],
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } },
    });
}
</script>
@endpush
