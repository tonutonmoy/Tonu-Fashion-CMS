@extends('layouts.guest')

@section('content')
<div class="card p-6 sm:p-8">
    <h1 class="text-2xl font-bold text-center mb-6">Create Account</h1>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="input" required>
        </div>
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="input" required>
        </div>
        <div>
            <label class="label">Phone</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" class="input" required placeholder="01XXXXXXXXX">
        </div>
        <div>
            <label class="label">Password</label>
            <input type="password" name="password" class="input" required>
        </div>
        <div>
            <label class="label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="input" required>
        </div>
        <button type="submit" class="btn-primary w-full">Register</button>
    </form>
    <p class="mt-4 text-center text-sm">Already have an account? <a href="{{ route('login') }}" class="text-brand-600 hover:underline">Login</a></p>
</div>
@endsection
