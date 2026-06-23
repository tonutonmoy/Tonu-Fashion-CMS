@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
    <h2 class="text-xl font-semibold">Products</h2>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">Add Product</a>
</div>

<form method="GET" class="card p-4 mb-4 flex flex-col sm:flex-row gap-3 flex-wrap" data-admin-auto-filter>
    <input type="text" name="search" value="{{ request('search') }}" class="input flex-1 min-w-[12rem]" placeholder="Search name or SKU…"
           data-search-suggest="{{ route('admin.search.suggest', ['type' => 'products']) }}">
    <select name="category_id" class="input sm:w-44">
        <option value="">All categories</option>
        @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <select name="status" class="input sm:w-36">
        <option value="">All statuses</option>
        @foreach(\App\Enums\RecordStatus::cases() as $status)
        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <a href="{{ route('admin.products.index') }}" class="btn-secondary">Reset</a>
    <span class="text-xs text-gray-400 self-center">Filters apply automatically</span>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">SKU</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-right">Price</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($products as $product)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $product->sku }}</td>
                <td class="px-4 py-3">{{ $product->category?->name }}</td>
                <td class="px-4 py-3 text-right">{{ format_bdt($product->effective_price) }}</td>
                <td class="px-4 py-3"><span class="badge {{ $product->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $product->status->label() }}</span></td>
                <td class="px-4 py-3 text-right">
                    <x-admin.action-group>
                        <x-admin.action-btn variant="edit" :href="route('admin.products.edit', $product)" />
                        <x-admin.action-btn variant="delete" :action="route('admin.products.destroy', $product)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$product->name" />
                    </x-admin.action-group>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
