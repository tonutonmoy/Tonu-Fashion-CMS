@extends('layouts.admin')
@section('title', 'Add Coupon')
@section('content')
<form action="{{ route('admin.coupons.store') }}" method="POST" class="max-w-xl card p-6 space-y-4">
    @csrf
    <div><label class="label">Code</label><input name="code" class="input uppercase" required></div>
    <div><label class="label">Type</label><select name="type" class="input"><option value="percentage">Percentage</option><option value="fixed">Fixed</option></select></div>
    <div><label class="label">Value</label><input type="number" name="value" step="0.01" class="input" required></div>
    <div><label class="label">Min Order Amount</label><input type="number" name="min_order_amount" class="input"></div>
    <div><label class="label">Usage Limit</label><input type="number" name="usage_limit" class="input"></div>
    <div><label class="label">Expires At</label><input type="date" name="expires_at" class="input"></div>
    <div><label class="label">Status</label><select name="status" class="input"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    <button class="btn-primary">Save</button>
</form>
@endsection
