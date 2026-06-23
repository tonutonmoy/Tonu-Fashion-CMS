@extends('layouts.admin')
@section('title', 'Licenses')
@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold">License Management</h2>
    <p class="text-sm text-gray-500">Current server domain: <strong>{{ $currentDomain }}</strong></p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-4"><p class="text-sm text-gray-500">Total</p><p class="text-2xl font-bold">{{ $stats['total'] }}</p></div>
    <div class="card p-4"><p class="text-sm text-gray-500">Active</p><p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p></div>
    <div class="card p-4"><p class="text-sm text-gray-500">Expired</p><p class="text-2xl font-bold text-orange-600">{{ $stats['expired'] }}</p></div>
    <div class="card p-4"><p class="text-sm text-gray-500">Suspended</p><p class="text-2xl font-bold text-red-600">{{ $stats['suspended'] }}</p></div>
</div>

<form method="GET" class="card p-4 mb-6 flex flex-col sm:flex-row gap-3" data-admin-auto-filter>
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search key, domain, customer..." class="input flex-1"
           data-search-suggest="{{ route('admin.search.suggest', ['type' => 'licenses']) }}">
    <select name="status" class="input sm:w-48">
        <option value="">All statuses</option>
        @foreach(\App\Enums\LicenseStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <a href="{{ route('admin.license.index') }}" class="btn-secondary">Reset</a>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">License Key</th>
                    <th class="px-4 py-3 text-left">Domain</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Expires</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($licenses as $license)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs">{{ $license->license_key }}</td>
                    <td class="px-4 py-3">{{ $license->licensed_domain ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <p>{{ $license->customer_name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $license->customer_email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge bg-{{ $license->status->color() }}-100 text-{{ $license->status->color() }}-800">{{ $license->status->label() }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $license->expires_at?->format('d M Y') ?? 'Never' }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <x-admin.action-group>
                            <x-admin.action-btn variant="edit" :href="route('admin.license.edit', $license)" />
                        </x-admin.action-group>
                        <div class="mt-1 flex flex-wrap justify-end gap-1 text-xs">
                        @if($license->status === \App\Enums\LicenseStatus::Active)
                            <form action="{{ route('admin.license.suspend', $license) }}" method="POST" class="inline">@csrf @method('PATCH')<button class="text-orange-600">Suspend</button></form>
                        @else
                            <form action="{{ route('admin.license.activate', $license) }}" method="POST" class="inline">@csrf @method('PATCH')<button class="text-green-600">Activate</button></form>
                        @endif
                        <form action="{{ route('admin.license.expire', $license) }}" method="POST" class="inline">@csrf @method('PATCH')<button class="text-gray-600">Expire</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No licenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $licenses->links() }}</div>
@endsection
