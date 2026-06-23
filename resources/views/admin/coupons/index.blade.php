@extends('layouts.admin')
@section('title', 'Coupons')
@section('content')
<div class="flex justify-between mb-6"><h2 class="text-xl font-semibold">Coupons</h2><a href="{{ route('admin.coupons.create') }}" class="btn-primary">Add Coupon</a></div>
<div class="card overflow-hidden">
    <table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Code</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Value</th><th class="px-4 py-3 text-left">Expires</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
    <tbody class="divide-y">@foreach($coupons as $coupon)<tr><td class="px-4 py-3 font-mono">{{ $coupon->code }}</td><td class="px-4 py-3">{{ $coupon->type->label() }}</td><td class="px-4 py-3">{{ $coupon->type->value === 'percentage' ? $coupon->value.'%' : format_bdt($coupon->value) }}</td><td class="px-4 py-3">{{ $coupon->expires_at?->format('d M Y') ?? 'Never' }}</td><td class="px-4 py-3 text-right"><x-admin.action-group><x-admin.action-btn variant="edit" :href="route('admin.coupons.edit', $coupon)" /><x-admin.action-btn variant="delete" :action="route('admin.coupons.destroy', $coupon)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$coupon->code" /></x-admin.action-group></td></tr>@endforeach</tbody></table>
</div>
{{ $coupons->links() }}
@endsection
