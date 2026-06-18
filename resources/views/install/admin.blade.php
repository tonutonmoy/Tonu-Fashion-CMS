@extends('install.layout')
@section('title', 'Admin Account')
@section('step', 4)
@section('content')
<h2 class="text-2xl font-bold mb-2">Admin Account</h2>
<p class="text-gray-600 mb-6">Create your Super Admin account for the admin panel.</p>

<form action="{{ route('install.admin') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="label">Admin Name</label>
        <input name="name" value="{{ old('name', $data['name']) }}" class="input w-full" required>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Email / Username</label>
            <input type="text" name="email" value="{{ old('email', $data['email']) }}" class="input w-full" required>
        </div>
        <div>
            <label class="label">Phone (optional)</label>
            <input name="phone" value="{{ old('phone', $data['phone']) }}" class="input w-full">
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Password</label>
            <input type="password" name="password" class="input w-full" required minlength="{{ config('admin.password_min_length') }}">
        </div>
        <div>
            <label class="label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="input w-full" required>
        </div>
    </div>
    <p class="text-xs text-gray-500">Role: <strong>SUPER_ADMIN</strong></p>
    <div class="flex justify-between pt-4">
        <a href="{{ route('install.store') }}" class="btn-secondary">← Previous</a>
        <button type="submit" class="btn-primary">Next: Installation →</button>
    </div>
</form>
@endsection
