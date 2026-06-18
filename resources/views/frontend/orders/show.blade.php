@extends('layouts.frontend')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <h1 class="text-2xl font-bold mb-2">Order {{ $order->order_number }}</h1>

    <p class="text-sm text-gray-500 mb-6">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>

    <div class="card p-6 mb-6">

        <span class="badge bg-green-100 text-green-800">{{ $order->status->label() }}</span>

        @foreach($order->items as $item)

        <div class="flex justify-between py-3 border-b text-sm">

            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>

            <span>{{ format_bdt($item->total_price) }}</span>

        </div>

        @endforeach

        <div class="flex justify-between text-sm py-2"><span>Shipping</span><span>{{ format_bdt($order->shipping_cost) }}</span></div>

        <div class="flex justify-between font-bold text-lg mt-4 pt-4 border-t">

            <span>Total (COD)</span>

            <span>{{ format_bdt($order->total) }}</span>

        </div>

    </div>

    <div class="card p-6 text-sm">

        <h3 class="font-semibold mb-2">Delivery Address</h3>

        <p>{{ $order->customer_name }} · {{ $order->customer_phone }}</p>

        <p class="text-gray-600 mt-1">

            {{ $order->shipping_address }}

            @if($order->shipping_area), {{ $order->shipping_area }}@endif

            , {{ $order->shipping_district }}, {{ $order->shipping_division }}

        </p>

        @if($order->order_note)

        <p class="text-gray-500 mt-2">Note: {{ $order->order_note }}</p>

        @endif

    </div>

</div>

@include('themes.shared.partials.order-purchase-tracking')

@endsection

