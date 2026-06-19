@extends(theme_layout())

@section('content')
<div class="theme-container py-4 sm:py-8">
    <h1 class="theme-page-title mb-4 sm:mb-6">{{ __('common.wishlist') }}</h1>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
        @forelse($items as $item)
            <x-product-card :product="$item->product" />
        @empty
            <p class="col-span-full text-gray-500">{{ __('common.no_results') }}</p>
        @endforelse
    </div>
</div>
@endsection
