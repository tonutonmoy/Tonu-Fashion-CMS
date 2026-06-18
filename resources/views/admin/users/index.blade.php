@extends('layouts.admin')
@section('title', 'Team Members')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Team Members</h2>
        <p class="text-sm text-gray-500">Create Admin or Staff users to help manage the store.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">Add Team Member</a>
</div>

<form method="GET" class="card p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="input flex-1">
    <select name="role" class="input sm:w-40">
        <option value="">All roles</option>
        @foreach(\App\Enums\UserRole::assignableTeamRoles() as $role)
        <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
        @endforeach
    </select>
    <button class="btn-primary">Search</button>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">Permissions</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $member)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $member->name }}</td>
                <td class="px-4 py-3">{{ $member->email }}</td>
                <td class="px-4 py-3">{{ $member->role->label() }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @forelse($member->enabledAdminPermissionLabels() as $label)
                        <span class="inline-flex px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs">{{ $label }}</span>
                        @empty
                        <span class="text-gray-400 text-xs">None</span>
                        @endforelse
                    </div>
                </td>
                <td class="px-4 py-3">{{ $member->status->label() }}</td>
                <td class="px-4 py-3 text-right">
                    <x-admin.action-group>
                        <x-admin.action-btn variant="edit" :href="route('admin.users.edit', $member)" />
                        @if($member->id !== auth()->id())
                        <x-admin.action-btn variant="delete" :action="route('admin.users.destroy', $member)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$member->name" />
                        @endif
                    </x-admin.action-group>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No team members yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $users->links() }}
@endsection
