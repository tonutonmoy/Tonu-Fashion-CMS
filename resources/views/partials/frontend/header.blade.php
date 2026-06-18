<header class="sticky top-0 z-50 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-14 sm:h-16 gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg sm:text-xl flex-shrink-0">
                @if($storeSettings['logo'] ?? null)
                    <img src="{{ image_url($storeSettings['logo']) }}" alt="{{ $storeSettings['name'] }}" width="160" height="48" class="theme-logo-img">
                @else
                    {{ $storeSettings['name'] ?? 'Fashion Store' }}
                @endif
            </a>

            <form action="{{ route('shop.index') }}" method="GET" class="hidden md:flex flex-1 max-w-lg mx-4 relative" data-header-search>
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products..." class="input w-full pl-10 rounded-full bg-gray-50" aria-label="Search products">
            </form>

            <nav class="hidden lg:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('shop.index') }}" class="hover:text-brand-600">Shop</a>
                <a href="{{ route('wishlist.index') }}" class="hover:text-brand-600">Wishlist</a>
            </nav>

            <div class="flex items-center gap-3">
                <button type="button" data-open-cart class="relative p-2 hover:bg-gray-100 rounded-lg" aria-label="Open cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span data-cart-count class="absolute -top-0.5 -right-0.5 min-w-[1.25rem] h-5 px-1 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center {{ ($cartCount ?? 0) ? '' : 'hidden' }}">{{ $cartCount ?? 0 }}</span>
                </button>
            </div>
        </div>
        <form action="{{ route('shop.index') }}" method="GET" class="pb-3 md:hidden" data-header-search>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products..." class="input w-full pl-10 rounded-full" aria-label="Search products">
            </div>
        </form>
    </div>
</header>
