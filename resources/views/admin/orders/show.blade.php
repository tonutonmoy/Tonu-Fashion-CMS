@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')

@php $parcel = $order->courierParcel; @endphp

<div class="mb-4 flex flex-wrap gap-2">

    @if(!$parcel)

    <form action="{{ route('admin.orders.parcel.create', $order) }}" method="POST" class="inline">

        @csrf

        <button class="btn-primary text-sm">Create Parcel</button>

    </form>

    @else

    <form action="{{ route('admin.orders.parcel.sync', $order) }}" method="POST" class="inline">@csrf<button class="btn-primary text-sm bg-gray-600 hover:bg-gray-700">Sync Status</button></form>

    @endif

    <a href="{{ route('admin.orders.invoice', [$order, 'a4']) }}" target="_blank" class="btn-secondary text-sm">Print Invoice (A4)</a>

    <a href="{{ route('admin.orders.invoice', [$order, 'thermal']) }}" target="_blank" class="btn-secondary text-sm">Print Invoice (80mm)</a>

    <a href="{{ route('admin.orders.packing-slip', $order) }}" target="_blank" class="btn-secondary text-sm">Packing Slip</a>

    <a href="{{ route('admin.orders.label', $order) }}" target="_blank" class="btn-secondary text-sm">Print Label</a>

    @if($parcel?->tracking_url)

    <a href="{{ $parcel->tracking_url }}" target="_blank" class="btn-secondary text-sm">Tracking URL</a>

    @endif

</div>



<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 card p-6">

        <div class="flex items-center justify-between mb-4">

            <h3 class="font-semibold">Order Items</h3>

            <span class="badge bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">{{ $order->status->label() }}</span>

        </div>

        @foreach($order->items as $item)

        <div class="flex justify-between py-2 border-b text-sm">

            <span>{{ $item->product_name }} @if($item->size)({{ $item->size }}/{{ $item->color }})@endif × {{ $item->quantity }}</span>

            <span>{{ format_bdt($item->total_price) }}</span>

        </div>

        @endforeach

        <div class="mt-4 space-y-1 text-sm">

            <div class="flex justify-between"><span>Subtotal</span><span>{{ format_bdt($order->subtotal) }}</span></div>

            <div class="flex justify-between"><span>Discount</span><span>-{{ format_bdt($order->discount) }}</span></div>

            <div class="flex justify-between"><span>Shipping</span><span>{{ format_bdt($order->shipping_cost) }}</span></div>

            <div class="flex justify-between font-bold text-lg"><span>Total (COD)</span><span>{{ format_bdt($order->total) }}</span></div>

        </div>

    </div>

    <div class="space-y-4">

        <div class="card p-6">

            <h3 class="font-semibold mb-2">Shipping</h3>

            <p class="text-sm">{{ $order->customer_name }}</p>

            <p class="text-sm">{{ $order->customer_phone }}</p>

            @if($customerAccount)
            <p class="text-sm mt-2"><a href="{{ route('admin.customers.show', $customerAccount) }}" class="text-brand-600">View customer profile</a></p>
            @endif

            <p class="text-sm text-gray-600 mt-2">

                {{ $order->shipping_address }}

                @if($order->shipping_area), {{ $order->shipping_area }}@endif

                , {{ $order->shipping_district }}, {{ $order->shipping_division }}

            </p>

        </div>



        @if($parcel)

        <div class="card p-6 text-sm space-y-2">

            <h3 class="font-semibold">Courier Parcel</h3>

            <p><span class="text-gray-500">Courier:</span> {{ ucfirst($parcel->courier_name) }}</p>

            <p><span class="text-gray-500">Tracking:</span> {{ $parcel->tracking_code }}</p>

            <p><span class="text-gray-500">Status:</span> <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $parcel->current_status)) }}</span></p>

            <p class="text-xs text-gray-400">Last sync: {{ $parcel->last_synced_at?->diffForHumans() ?? 'Never' }}</p>

        </div>

        @endif



        @if($customerAccount)
        <div class="card p-6">
            <h3 class="font-semibold mb-3">Customer Restrictions</h3>
            @include('admin.customers._restrictions', [
                'customer' => $customerAccount,
                'action' => route('admin.orders.customers.restrictions', $customerAccount),
            ])
        </div>
        @endif



        <div class="card p-6">

            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="space-y-3">

                @csrf @method('PATCH')

                <label class="label">Update Status</label>

                <select name="status" class="input">

                    @foreach($statuses as $status)<option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>@endforeach

                </select>

                <button class="btn-primary w-full">Update</button>

            </form>

        </div>

    </div>

</div>



@if($parcel && $parcel->histories->isNotEmpty())

<div class="card p-6 mt-6">

    <h3 class="font-semibold mb-4">Tracking History</h3>

    <div class="space-y-3">

        @foreach($parcel->histories as $history)

        <div class="flex justify-between text-sm border-b pb-2">

            <div>

                <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>

                <p class="text-gray-500">{{ $history->description }}</p>

            </div>

            <span class="text-gray-400 text-xs">{{ $history->recorded_at?->format('d M Y H:i') }}</span>

        </div>

        @endforeach

    </div>

</div>

@endif

@endsection

