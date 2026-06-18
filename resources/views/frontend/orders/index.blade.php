@extends('layouts.frontend')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">My Orders</h1>
    <div class="space-y-4">
        @forelse($orders as $order)
        <a href="{{ route('orders.show', $order) }}" class="card p-4 block hover:shadow-md">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium">{{ $order->order_number }}</p>
                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="badge bg-gray-100">{{ $order->status->label() }}</span>
                    <p class="font-semibold mt-1">{{ format_bdt($order->total) }}</p>
                </div>
            </div>
        </a>
        @empty
        <p class="text-gray-500">No orders yet.</p>
        @endforelse
    </div>
    {{ $orders->links() }}
</div>
@endsection
