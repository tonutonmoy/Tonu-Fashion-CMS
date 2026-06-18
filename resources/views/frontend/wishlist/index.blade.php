@extends('layouts.frontend')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">Wishlist</h1>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse($items as $item)<x-product-card :product="$item->product" />@empty<p class="col-span-full text-gray-500">Wishlist is empty.</p>@endforelse
    </div>
</div>
@endsection
