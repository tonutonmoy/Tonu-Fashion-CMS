@extends('layouts.admin')
@section('title', $customer->name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <h3 class="font-semibold mb-4">Customer Info</h3>
        <p>{{ $customer->email }}</p>
        <p>{{ $customer->phone ?? '—' }}</p>
        <p class="text-sm text-gray-500 mt-2">Joined {{ $customer->created_at->format('d M Y') }}</p>

        <div class="mt-6 pt-6 border-t border-gray-100">
            <h4 class="font-medium mb-3">Block / Unblock</h4>
            @include('admin.customers._restrictions', [
                'customer' => $customer,
                'action' => route('admin.customers.restrictions', $customer),
            ])
        </div>
    </div>
    <div class="card p-6">
        <h3 class="font-semibold mb-4">Order History</h3>
        @forelse($orders as $order)
        <div class="flex justify-between py-2 border-b text-sm">
            <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-600">{{ $order->order_number }}</a>
            <span>{{ format_bdt($order->total) }}</span>
        </div>
        @empty
        <p class="text-gray-500">No orders.</p>
        @endforelse
        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
