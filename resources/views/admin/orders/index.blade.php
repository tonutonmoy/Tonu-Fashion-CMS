@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
@php
    $scope = request('scope', 'today');
@endphp

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
    <div class="flex gap-2">
        <a href="{{ route('admin.orders.index', array_merge(request()->except('scope', 'page'), ['scope' => 'today'])) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ $scope === 'today' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Today
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->except('scope', 'page'), ['scope' => 'all'])) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ $scope === 'all' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All
        </a>
    </div>
    <a href="{{ route('admin.orders.create') }}" class="btn-primary shrink-0">Create Custom Order</a>
</div>

<form method="GET" class="card p-4 mb-4 space-y-3">
    <input type="hidden" name="scope" value="{{ $scope }}">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Order ID or phone…" class="input lg:col-span-2">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach($statuses as $status)
            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input" title="From date">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="input" title="To date">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('admin.orders.index', ['scope' => $scope]) }}" class="btn-secondary">Reset</a>
    </div>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Order</th>
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-left">Phone</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
            <tr>
                <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand-600 font-medium">{{ $order->order_number }}</a></td>
                <td class="px-4 py-3">{{ $order->customer_name }}</td>
                <td class="px-4 py-3">{{ $order->customer_phone }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3">
                    <span class="badge bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">{{ $order->status->label() }}</span>
                </td>
                <td class="px-4 py-3 text-right font-medium">{{ format_bdt($order->total) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $orders->links() }}
@endsection
