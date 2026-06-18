@extends('layouts.admin')
@section('title', 'Bangladesh Shipping')
@section('content')
@include('admin.marketing._nav')
<form action="{{ route('admin.marketing.shipping') }}" method="POST" class="max-w-xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div><label class="label">Inside Dhaka (৳)</label><input type="number" name="inside_dhaka" value="{{ $settings['inside_dhaka'] }}" class="input" required></div>
    <div><label class="label">Outside Dhaka (৳)</label><input type="number" name="outside_dhaka" value="{{ $settings['outside_dhaka'] }}" class="input" required></div>
    <div><label class="label">Free Shipping Above (৳)</label><input type="number" name="free_shipping_limit" value="{{ $settings['free_shipping_limit'] }}" class="input" required></div>
    <p class="text-sm text-gray-500">Dhaka division districts: Dhaka, Gazipur, Narayanganj qualify for inside Dhaka rate.</p>
    <button class="btn-primary">Save Shipping</button>
</form>
@endsection
