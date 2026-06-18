@extends('layouts.guest')

@section('content')
<div class="card p-6 sm:p-8">
    <h1 class="text-2xl font-bold text-center mb-6">Forgot Password</h1>
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="input" required>
        </div>
        <button type="submit" class="btn-primary w-full">Send Reset Link</button>
    </form>
    <p class="mt-4 text-center text-sm"><a href="{{ route('login') }}" class="text-brand-600 hover:underline">Back to Login</a></p>
</div>
@endsection
