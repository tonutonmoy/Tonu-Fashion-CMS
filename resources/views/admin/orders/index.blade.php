@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
    <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order, phone, name..." class="input flex-1">
        <select name="status" class="input sm:w-44">
            <option value="">All statuses</option>
            @foreach($statuses as $status)
            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="btn-primary">Filter</button>
    </form>
    <a href="{{ route('admin.orders.create') }}" class="btn-primary shrink-0">Create Custom Order</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Order</th><th class="px-4 py-3 text-left">Customer</th><th class="px-4 py-3 text-left">Phone</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Total</th></tr></thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
            <tr>
                <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand-600">{{ $order->order_number }}</a></td>
                <td class="px-4 py-3">{{ $order->customer_name }}</td>
                <td class="px-4 py-3">{{ $order->customer_phone }}</td>
                <td class="px-4 py-3">{{ $order->status->label() }}</td>
                <td class="px-4 py-3 text-right">{{ format_bdt($order->total) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $orders->links() }}
@endsection
