@extends('layouts.admin')
@section('title', 'Edit License')
@section('content')
<h2 class="text-xl font-semibold mb-2">Edit License</h2>
<p class="font-mono text-sm text-gray-500 mb-6">{{ $license->license_key }}</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form action="{{ route('admin.license.update', $license) }}" method="POST" class="card p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="label">Customer Name</label><input name="customer_name" class="input w-full" value="{{ old('customer_name', $license->customer_name) }}"></div>
            <div><label class="label">Customer Email</label><input type="email" name="customer_email" class="input w-full" value="{{ old('customer_email', $license->customer_email) }}"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="label">Licensed Domain</label><input name="licensed_domain" class="input w-full" value="{{ old('licensed_domain', $license->licensed_domain) }}"></div>
            <div><label class="label">Plan</label><input name="plan" class="input w-full" value="{{ old('plan', $license->plan) }}"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Status</label>
                <select name="status" class="input w-full">
                    @foreach(\App\Enums\LicenseStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $license->status->value) === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="label">Expires At</label><input type="date" name="expires_at" class="input w-full" value="{{ old('expires_at', $license->expires_at?->format('Y-m-d')) }}"></div>
        </div>
        <div><label class="label">Notes</label><textarea name="notes" rows="3" class="input w-full">{{ old('notes', $license->notes) }}</textarea></div>
        <button class="btn-primary">Save Changes</button>
    </form>

    <div class="space-y-4">
        <div class="card p-6 text-sm space-y-2">
            <h3 class="font-semibold mb-2">License Details</h3>
            <p><span class="text-gray-500">Issued:</span> {{ $license->issued_at?->format('d M Y H:i') ?? '—' }}</p>
            <p><span class="text-gray-500">Last check:</span> {{ $license->last_check_at?->diffForHumans() ?? 'Never' }}</p>
            <p><span class="text-gray-500">Last IP:</span> {{ $license->last_ip ?? '—' }}</p>
            <p><span class="text-gray-500">Signature:</span> <span class="font-mono text-xs">{{ \Illuminate\Support\Str::limit($license->verification_signature, 24) }}</span></p>
        </div>

        <form action="{{ route('admin.license.assign-domain', $license) }}" method="POST" class="card p-6 space-y-3">
            @csrf @method('PATCH')
            <h3 class="font-semibold">Quick Assign Domain</h3>
            <input name="licensed_domain" class="input w-full" placeholder="customerdomain.com" required>
            <button class="btn-secondary w-full">Assign Domain</button>
        </form>

        <form action="{{ route('admin.license.destroy', $license) }}" method="POST" onsubmit="return confirm('Delete this license?')">
            @csrf @method('DELETE')
            <button class="text-red-600 text-sm">Delete License</button>
        </form>
    </div>
</div>
@endsection
