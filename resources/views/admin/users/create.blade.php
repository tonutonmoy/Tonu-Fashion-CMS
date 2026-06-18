@extends('layouts.admin')
@section('title', 'Add Team Member')
@section('content')
<h2 class="text-xl font-semibold mb-6">Add Team Member</h2>
<form action="{{ route('admin.users.store') }}" method="POST" class="max-w-2xl card p-6 space-y-4">
    @csrf
    <div><label class="label">Name</label><input name="name" class="input w-full" value="{{ old('name') }}" required></div>
    <div><label class="label">Email</label><input type="email" name="email" class="input w-full" value="{{ old('email') }}" required></div>
    <div><label class="label">Phone</label><input name="phone" class="input w-full" value="{{ old('phone') }}"></div>
    <div>
        <label class="label">Role</label>
        <select name="role" class="input w-full" required data-team-role-select>
            @foreach($roles as $role)
            <option value="{{ $role->value }}" @selected(old('role', \App\Enums\UserRole::Staff->value) === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Pick a role, then fine-tune permissions below.</p>
    </div>
    @include('admin.users._permissions', [
        'permissionMap' => old('permissions', \App\Enums\AdminPermission::defaultMapForRole(\App\Enums\UserRole::tryFrom(old('role', \App\Enums\UserRole::Staff->value)) ?? \App\Enums\UserRole::Staff)),
    ])
    <div><label class="label">Password</label><input type="password" name="password" class="input w-full" required></div>
    <div><label class="label">Confirm Password</label><input type="password" name="password_confirmation" class="input w-full" required></div>
    <div class="flex gap-3">
        <button class="btn-primary">Create User</button>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
