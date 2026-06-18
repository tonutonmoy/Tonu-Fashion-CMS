@extends('layouts.frontend')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">My Profile</h1>
    <form action="{{ route('profile.update') }}" method="POST" class="card p-6 space-y-4 mb-8">
        @csrf @method('PUT')
        <div><label class="label">Name</label><input name="name" value="{{ old('name', $user->name) }}" class="input" required></div>
        <div><label class="label">Email</label><input name="email" type="email" value="{{ old('email', $user->email) }}" class="input" required></div>
        <div><label class="label">Phone</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="input"></div>
        <div><label class="label">New Password</label><input name="password" type="password" class="input"></div>
        <div><label class="label">Confirm Password</label><input name="password_confirmation" type="password" class="input"></div>
        <button class="btn-primary">Update Profile</button>
    </form>

    <h2 class="text-lg font-semibold mb-4">Saved Addresses</h2>
    @foreach($user->addresses as $address)
    <div class="card p-4 mb-3 text-sm">
        <p class="font-medium">{{ $address->name }} · {{ $address->phone }}</p>
        <p class="text-gray-600">{{ $address->full_address }}</p>
    </div>
    @endforeach

    <form action="{{ route('profile.addresses.store') }}" method="POST" class="card p-6 space-y-4 mt-6">
        @csrf
        <h3 class="font-semibold">Add Address</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="label">Name</label><input name="name" class="input" required></div>
            <div><label class="label">Phone</label><input name="phone" class="input" required></div>
            <div><label class="label">Division</label><input name="division" class="input" required></div>
            <div><label class="label">District</label><input name="district" class="input" required></div>
            <div class="sm:col-span-2"><label class="label">Address</label><textarea name="address_line" class="input" rows="2" required></textarea></div>
        </div>
        <button class="btn-secondary">Save Address</button>
    </form>
</div>
@endsection
