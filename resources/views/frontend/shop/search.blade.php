@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <form method="GET" class="mb-8 flex gap-2">
        <input type="search" name="q" value="{{ $query }}" placeholder="Search products..." class="input flex-1">
        <button type="submit" class="btn-primary">Search</button>
    </form>
    @if($query)
        <h1 class="text-xl font-semibold mb-6">Results for "{{ $query }}"</h1>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <p class="col-span-full text-gray-500">No products found.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    @endif
</div>
@endsection
