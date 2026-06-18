@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between mb-6">
    <h2 class="text-xl font-semibold">Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">Add Category</a>
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Products</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
        <tbody class="divide-y">
            @foreach($categories as $category)
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
            @endforeach
        </tbody>
    </table>
</div>
{{ $categories->links() }}
@endsection
