@extends('layouts.guest')

@section('content')
<div class="card p-6 sm:p-8">
    <h1 class="text-2xl font-bold text-center mb-6">Reset Password</h1>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="input" required>
        </div>
        <div>
            <label class="label">New Password</label>
            <input type="password" name="password" class="input" required>
        </div>
        <div>
            <label class="label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="input" required>
        </div>
        <button type="submit" class="btn-primary w-full">Reset Password</button>
    </form>
</div>
@endsection
