@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-semibold">Products</h2>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">Add Product</a>
</div>
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
            @foreach($products as $product)
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
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
