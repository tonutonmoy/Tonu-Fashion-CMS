@extends('layouts.admin')
@section('title', 'Inventory Log')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Stock Movement History</h2>
        <p class="text-sm text-gray-500">Reserve, deduct, rollback, and manual adjustments</p>
    </div>
    <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">Back to Inventory</a>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3 text-left">Order</th>
                    <th class="px-4 py-3 text-left">Note</th>
                    <th class="px-4 py-3 text-left">Staff</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movements as $movement)
                @php
                    $variant = $movement->product_variant_id ? ($variants[(string) $movement->product_variant_id] ?? null) : null;
                    $product = $variant?->product ?? ($movement->product_id ? ($products[(string) $movement->product_id] ?? null) : null);
                    $type = $movement->type instanceof \App\Enums\StockMovementType ? $movement->type : \App\Enums\StockMovementType::from($movement->type);
                @endphp
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $movement->created_at?->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <span class="font-medium">{{ $product?->name ?? '—' }}</span>
                        @if($variant)
                        <span class="block text-xs text-gray-500">{{ $variant->display_name }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge bg-{{ $type->color() }}-100 text-{{ $type->color() }}-800">{{ $type->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">{{ $movement->quantity }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $movement->order_id ? '#'.$movement->order_id : '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate" title="{{ $movement->note }}">{{ $movement->note ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $movement->admin_id ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No movements recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $movements->withQueryString()->links() }}</div>
@endsection
