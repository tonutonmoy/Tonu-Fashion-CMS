@extends('layouts.admin')
@section('title', 'Edit Team Member')
@section('content')
<h2 class="text-xl font-semibold mb-6">Edit {{ $member->name }}</h2>
<form action="{{ route('admin.users.update', $member) }}" method="POST" class="max-w-2xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div><label class="label">Name</label><input name="name" class="input w-full" value="{{ old('name', $member->name) }}" required></div>
    <div><label class="label">Email</label><input type="email" name="email" class="input w-full" value="{{ old('email', $member->email) }}" required></div>
    <div><label class="label">Phone</label><input name="phone" class="input w-full" value="{{ old('phone', $member->phone) }}"></div>
    <div>
        <label class="label">Role</label>
        <select name="role" class="input w-full" required data-team-role-select>
            @foreach($roles as $role)
            <option value="{{ $role->value }}" @selected(old('role', $member->role->value) === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
    </div>
    @include('admin.users._permissions', ['member' => $member, 'permissionMap' => old('permissions', $member->adminPermissionsMap())])
    <div>
        <label class="label">Status</label>
        <select name="status" class="input w-full" required>
            <option value="active" @selected(old('status', $member->status->value) === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $member->status->value) === 'inactive')>Inactive</option>
        </select>
    </div>
    <div><label class="label">New Password (optional)</label><input type="password" name="password" class="input w-full"></div>
    <div><label class="label">Confirm Password</label><input type="password" name="password_confirmation" class="input w-full"></div>
    <div class="flex gap-3">
        <button class="btn-primary">Save Changes</button>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
