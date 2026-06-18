@extends('layouts.frontend')
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-2xl font-bold mb-2">Track Your Order</h1>
    <p class="text-gray-500 mb-6">Enter your phone number and order ID to see delivery status.</p>

    <form action="{{ route('track-order.track') }}" method="POST" class="card p-6 space-y-4 mb-8">
        @csrf
        <div>
            <label class="label">Phone Number</label>
            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" class="input w-full" placeholder="01XXXXXXXXX" pattern="01[0-9]{9}" required>
        </div>
        <div>
            <label class="label">Order Number</label>
            <input type="text" name="order_number" value="{{ old('order_number', request('order_number')) }}" class="input w-full" placeholder="ORD-XXXXXXXX" required>
        </div>
        <button class="btn-primary w-full">Track Order</button>
    </form>

    @if($order)
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-500">Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <span class="badge bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">{{ $order->status->label() }}</span>
        </div>

        @if($order->courierParcel)
        @php $parcel = $order->courierParcel; @endphp
        <div class="bg-gray-50 rounded-lg p-4 text-sm space-y-1">
            <p><strong>Courier:</strong> {{ ucfirst($parcel->courier_name) }}</p>
            <p><strong>Tracking:</strong> {{ $parcel->tracking_code }}</p>
            <p><strong>Parcel Status:</strong> {{ ucfirst(str_replace('_', ' ', $parcel->current_status)) }}</p>
            @if($parcel->tracking_url)
            <a href="{{ $parcel->tracking_url }}" target="_blank" class="text-brand-600 hover:underline">Open courier tracking →</a>
            @endif
        </div>
        @endif

        <div>
            <h3 class="font-semibold mb-3">Tracking History</h3>
            @forelse($order->courierParcel?->histories ?? [] as $history)
            <div class="flex justify-between py-3 border-b text-sm">
                <div>
                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                    @if($history->description)<p class="text-gray-500">{{ $history->description }}</p>@endif
                </div>
                <span class="text-gray-400 text-xs">{{ $history->recorded_at?->format('d M Y H:i') }}</span>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Tracking updates will appear here once the parcel is created.</p>
            @endforelse
        </div>

        <div class="text-sm">
            <h3 class="font-semibold mb-2">Items</h3>
            @foreach($order->items as $item)
            <div class="flex justify-between py-1"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span>{{ format_bdt($item->total_price) }}</span></div>
            @endforeach
            <div class="flex justify-between font-bold mt-2 pt-2 border-t"><span>Total (COD)</span><span>{{ format_bdt($order->total) }}</span></div>
        </div>
    </div>
    @endif
</div>
@endsection
