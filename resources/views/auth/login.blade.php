@extends('layouts.guest')

@section('content')
<div class="card p-6 sm:p-8">
    <h1 class="text-2xl font-bold text-center mb-6">Login</h1>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus>
        </div>
        <div>
            <label class="label">Password</label>
            <input type="password" name="password" class="input" required>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            Remember me
        </label>
        <button type="submit" class="btn-primary w-full">Login</button>
    </form>
    <div class="mt-4 text-center text-sm space-y-2">
        <a href="{{ route('password.request') }}" class="text-brand-600 hover:underline">Forgot password?</a>
        <p>Don't have an account? <a href="{{ route('register') }}" class="text-brand-600 hover:underline">Register</a></p>
    </div>
</div>
@endsection
