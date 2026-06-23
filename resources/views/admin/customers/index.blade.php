@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<form method="GET" class="card p-4 mb-4 flex flex-col sm:flex-row gap-3" data-admin-auto-filter>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." class="input flex-1"
           data-search-suggest="{{ route('admin.search.suggest', ['type' => 'customers']) }}">
    <select name="status" class="input sm:w-40">
        <option value="">All accounts</option>
        <option value="active" @selected(request('status') === 'active')>Active</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Blocked</option>
    </select>
    <a href="{{ route('admin.customers.index') }}" class="btn-secondary">Reset</a>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Phone</th>
                <th class="px-4 py-3 text-left">Orders</th>
                <th class="px-4 py-3 text-left">Account</th>
                <th class="px-4 py-3 text-left">Restrictions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($customers as $customer)
            <tr>
                <td class="px-4 py-3"><a href="{{ route('admin.customers.show', $customer) }}" class="text-brand-600">{{ $customer->name }}</a></td>
                <td class="px-4 py-3">{{ $customer->email }}</td>
                <td class="px-4 py-3">{{ $customer->phone ?? '—' }}</td>
                <td class="px-4 py-3">{{ $customer->orders_count }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $customer->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $customer->status->value === 'active' ? 'Active' : 'Blocked' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-500 space-x-2">
                    @if($customer->order_blocked)<span class="badge bg-orange-100 text-orange-800">No orders</span>@endif
                    @if($customer->blog_blocked)<span class="badge bg-purple-100 text-purple-800">No blog</span>@endif
                    @if(!$customer->order_blocked && !$customer->blog_blocked && $customer->status->value === 'active')—@endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No customers found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $customers->links() }}
@endsection
