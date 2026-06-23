@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
@php $parcel = $order->courierParcel; @endphp

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-sm text-gray-500">Order</p>
        <h1 class="text-2xl font-bold tracking-tight">{{ $order->order_number }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('d M Y, h:i A') }} · COD {{ format_bdt($order->total) }}</p>
    </div>
    <span class="badge self-start bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800 text-sm px-3 py-1">{{ $order->status->label() }}</span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <div class="card p-6">
            <h2 class="font-semibold text-lg mb-4">Items</h2>
            <div class="divide-y">
                @foreach($order->items as $item)
                <div class="flex justify-between gap-4 py-3 text-sm">
                    <div>
                        <p class="font-medium">{{ $item->product_name }}</p>
                        @if($item->size || $item->color)
                        <p class="text-gray-500 text-xs">{{ $item->size }} / {{ $item->color }} · Qty {{ $item->quantity }}</p>
                        @else
                        <p class="text-gray-500 text-xs">Qty {{ $item->quantity }}</p>
                        @endif
                    </div>
                    <p class="font-medium whitespace-nowrap">{{ format_bdt($item->total_price) }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span>{{ format_bdt($order->subtotal) }}</span></div>
                @if($order->discount > 0)
                <div class="flex justify-between text-green-700"><span>Discount</span><span>-{{ format_bdt($order->discount) }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-600">Shipping</span><span>{{ format_bdt($order->shipping_cost) }}</span></div>
                <div class="flex justify-between text-lg font-bold pt-2"><span>Total</span><span>{{ format_bdt($order->total) }}</span></div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="font-semibold mb-3">Customer & delivery</h2>
            <p class="font-medium">{{ $order->customer_name }}</p>
            <p class="text-sm text-gray-600">{{ $order->customer_phone }}</p>
            <p class="text-sm text-gray-600 mt-2">{{ $order->shipping_address }}@if($order->shipping_area), {{ $order->shipping_area }}@endif, {{ $order->shipping_district }}, {{ $order->shipping_division }}</p>
            @if($customerAccount)
            <a href="{{ route('admin.customers.show', $customerAccount) }}" class="inline-block mt-3 text-sm text-brand-600 hover:underline">View customer profile →</a>
            @endif
        </div>

        @if($parcel && $parcel->histories->isNotEmpty())
        <div class="card p-6">
            <h2 class="font-semibold mb-4">Tracking history</h2>
            <div class="space-y-3">
                @foreach($parcel->histories as $history)
                <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                    <div>
                        <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                        <p class="text-gray-500 text-xs">{{ $history->description }}</p>
                    </div>
                    <span class="text-gray-400 text-xs whitespace-nowrap">{{ $history->recorded_at?->format('d M H:i') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="card p-6 space-y-3">
            <h2 class="font-semibold">Quick actions</h2>
            @if(!$parcel)
            <form action="{{ route('admin.orders.parcel.create', $order) }}" method="POST">@csrf
                <button type="submit" class="btn-primary w-full">📦 Create courier parcel</button>
            </form>
            @else
            <form action="{{ route('admin.orders.parcel.sync', $order) }}" method="POST">@csrf
                <button type="submit" class="btn-primary w-full bg-gray-700 hover:bg-gray-800">↻ Sync parcel status</button>
            </form>
            @endif
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('admin.orders.invoice', [$order, 'a4']) }}" target="_blank" class="btn-secondary text-xs text-center">Invoice A4</a>
                <a href="{{ route('admin.orders.invoice', [$order, 'thermal']) }}" target="_blank" class="btn-secondary text-xs text-center">Thermal</a>
                <a href="{{ route('admin.orders.packing-slip', $order) }}" target="_blank" class="btn-secondary text-xs text-center">Packing slip</a>
                <a href="{{ route('admin.orders.label', $order) }}" target="_blank" class="btn-secondary text-xs text-center">Label</a>
            </div>
            @if($parcel?->tracking_url)
            <a href="{{ $parcel->tracking_url }}" target="_blank" class="btn-secondary w-full text-center text-sm block">Open tracking link</a>
            @endif
        </div>

        @if($parcel)
        <div class="card p-6 text-sm space-y-2">
            <h2 class="font-semibold">Courier</h2>
            <p><span class="text-gray-500">Provider:</span> {{ ucfirst($parcel->courier_name) }}</p>
            <p><span class="text-gray-500">Tracking:</span> <span class="font-mono">{{ $parcel->tracking_code }}</span></p>
            <p><span class="text-gray-500">Status:</span> {{ ucfirst(str_replace('_', ' ', $parcel->current_status)) }}</p>
            <p class="text-xs text-gray-400">Synced {{ $parcel->last_synced_at?->diffForHumans() ?? 'never' }}</p>
        </div>
        @endif

        <div class="card p-6">
            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <h2 class="font-semibold">Change status</h2>
                <select name="status" class="input">
                    @foreach($statuses as $status)
                        @if($order->status === $status || $order->status->canTransitionTo($status))
                            <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="btn-primary w-full">Update status</button>
            </form>
        </div>

        @if($customerAccount)
        <div class="card p-6">
            <h2 class="font-semibold mb-3 text-sm">Customer restrictions</h2>
            @include('admin.customers._restrictions', [
                'customer' => $customerAccount,
                'action' => route('admin.orders.customers.restrictions', $customerAccount),
            ])
        </div>
        @endif
    </div>
</div>
@endsection
