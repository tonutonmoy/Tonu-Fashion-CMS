@extends('layouts.admin')
@section('title', 'Brands')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
    <h2 class="text-xl font-semibold">Brands</h2>
    <a href="{{ route('admin.brands.create') }}" class="btn-primary">Add Brand</a>
</div>

<form method="GET" class="card p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <input type="text" name="search" value="{{ request('search') }}" class="input flex-1" placeholder="Search name…">
    <select name="status" class="input sm:w-36">
        <option value="">All statuses</option>
        @foreach(\App\Enums\RecordStatus::cases() as $status)
        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">Filter</button>
    <a href="{{ route('admin.brands.index') }}" class="btn-secondary">Reset</a>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Products</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($brands as $brand)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $brand->name }}</td>
                <td class="px-4 py-3">{{ $brand->products_count }}</td>
                <td class="px-4 py-3">{{ $brand->status->label() }}</td>
                <td class="px-4 py-3 text-right">
                    <x-admin.action-group>
                        <x-admin.action-btn variant="edit" :href="route('admin.brands.edit', $brand)" />
                        <x-admin.action-btn variant="delete" :action="route('admin.brands.destroy', $brand)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$brand->name" />
                    </x-admin.action-group>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No brands found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $brands->links() }}
@endsection
