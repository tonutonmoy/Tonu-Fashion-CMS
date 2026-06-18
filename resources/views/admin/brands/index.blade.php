@extends('layouts.admin')
@section('title', 'Brands')
@section('content')
<div class="flex justify-between mb-6"><h2 class="text-xl font-semibold">Brands</h2><a href="{{ route('admin.brands.create') }}" class="btn-primary">Add Brand</a></div>
<div class="card overflow-hidden">
    <table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Products</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
    <tbody class="divide-y">@foreach($brands as $brand)<tr><td class="px-4 py-3">{{ $brand->name }}</td><td class="px-4 py-3">{{ $brand->products_count }}</td><td class="px-4 py-3 text-right"><x-admin.action-group><x-admin.action-btn variant="edit" :href="route('admin.brands.edit', $brand)" /></x-admin.action-group></td></tr>@endforeach</tbody></table>
</div>
{{ $brands->links() }}
@endsection
