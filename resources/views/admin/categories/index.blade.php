@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
    <h2 class="text-xl font-semibold">Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">Add Category</a>
</div>

<form method="GET" class="card p-4 mb-4 flex flex-col sm:flex-row gap-3" data-admin-auto-filter>
    <input type="text" name="search" value="{{ request('search') }}" class="input flex-1" placeholder="Search name…"
           data-search-suggest="{{ route('admin.search.suggest', ['type' => 'categories']) }}">
    <select name="status" class="input sm:w-36">
        <option value="">All statuses</option>
        @foreach(\App\Enums\RecordStatus::cases() as $status)
        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Reset</a>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Products</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
        <tbody class="divide-y">
            @forelse($categories as $category)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                <td class="px-4 py-3">{{ $category->products_count }}</td>
                <td class="px-4 py-3">{{ $category->status->label() }}</td>
                <td class="px-4 py-3 text-right">
                    <x-admin.action-group>
                        <x-admin.action-btn variant="edit" :href="route('admin.categories.edit', $category)" />
                        <x-admin.action-btn variant="delete" :action="route('admin.categories.destroy', $category)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$category->name" />
                    </x-admin.action-group>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No categories found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $categories->links() }}
@endsection
