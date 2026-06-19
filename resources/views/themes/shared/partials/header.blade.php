<header class="theme-header theme-header-{{ $themeSettings->header_style ?? 'default' }}">
    <div class="theme-container theme-header-inner">
        <div class="theme-header-top">
            <button type="button" id="mobile-menu-toggle" class="theme-icon-btn theme-header-mobile-toggle md:hidden shrink-0" aria-label="{{ __('common.menu') }}" aria-expanded="false" aria-controls="mobile-menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <a href="{{ route('home') }}" class="theme-logo theme-header-logo">
                @if($storeSettings['logo'] ?? null)
                    <img src="{{ image_url($storeSettings['logo']) }}" alt="{{ $storeSettings['name'] }}" loading="eager" decoding="async" width="160" height="48" class="theme-logo-img">
                @else
                    <span class="theme-logo-text">{{ $storeSettings['name'] ?? 'Fashion Store' }}</span>
                @endif
            </a>

            <form action="{{ route('shop.index') }}" method="GET" class="theme-header-search hidden md:flex flex-1 max-w-md mx-4 lg:mx-6 relative" data-header-search>
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ __('common.search_products') }}"
                    class="input w-full pl-10 rounded-full border-gray-200 bg-gray-50 focus:bg-white"
                    aria-label="{{ __('common.search_products') }}"
                >
            </form>

            <div class="theme-header-actions theme-header-mobile-actions flex items-center gap-1 shrink-0 md:hidden">
                <x-color-mode-toggle />
                <button type="button" data-open-cart class="theme-cart-btn relative theme-icon-btn" aria-label="{{ __('common.cart') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span data-cart-count class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center {{ ($cartCount ?? 0) ? '' : 'hidden' }}">{{ $cartCount ?? 0 }}</span>
                </button>
            </div>

            <nav class="theme-nav hidden md:flex items-center gap-1 xl:gap-2 flex-1 min-w-0 justify-end">
                @include('themes.shared.partials.menu-nav', ['items' => $headerMenu ?? collect()])
                <a href="{{ route('wishlist.index') }}" class="theme-nav-link shrink-0">{{ __('common.wishlist') }}</a>
                @if(config('admin.quick_login_enabled'))
                    <a href="{{ route('admin.quick-login') }}" class="theme-nav-link shrink-0" data-turbo="false">{{ __('common.admin') }}</a>
                @endif
                <div class="theme-header-actions flex items-center gap-1 shrink-0 ml-2 pl-2 border-l border-gray-200">
                    <x-color-mode-toggle />
                    <button type="button" data-open-cart class="theme-cart-btn relative theme-icon-btn" aria-label="{{ __('common.cart') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span data-cart-count class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center {{ ($cartCount ?? 0) ? '' : 'hidden' }}">{{ $cartCount ?? 0 }}</span>
                    </button>
                </div>
            </nav>
        </div>
    </div>

    <form action="{{ route('shop.index') }}" method="GET" class="theme-container pb-3 md:hidden px-3 theme-header-search-mobile" data-header-search>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('common.search_products') }}" class="input w-full pl-10 rounded-full min-h-[44px]" aria-label="{{ __('common.search_products') }}">
        </div>
    </form>
</header>

<div id="mobile-menu-overlay" class="mobile-menu-overlay fixed inset-0 bg-black/50 z-[80] hidden md:hidden" aria-hidden="true"></div>
<nav id="mobile-menu" class="mobile-menu-panel fixed top-0 right-0 h-full w-[min(100vw,20rem)] max-w-full bg-white z-[90] shadow-2xl transform translate-x-full transition-transform duration-300 md:hidden flex flex-col overflow-hidden" aria-label="Mobile navigation" aria-hidden="true">
    <div class="flex items-center justify-between gap-3 p-4 border-b border-gray-200 shrink-0">
        <span class="font-semibold text-base truncate">{{ $storeSettings['name'] ?? __('common.menu') }}</span>
        <button type="button" id="mobile-menu-close" class="theme-icon-btn shrink-0" aria-label="{{ __('common.close') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="mobile-menu-scroll flex-1 overflow-y-auto p-3 space-y-1">
        <a href="{{ route('home') }}" class="mobile-nav-link">{{ __('common.home') }}</a>
        @include('themes.shared.partials.menu-nav-mobile', ['items' => $headerMenu ?? collect()])
        <a href="{{ route('wishlist.index') }}" class="mobile-nav-link">{{ __('common.wishlist') }}</a>
        @if(config('admin.quick_login_enabled'))
            <a href="{{ route('admin.quick-login') }}" class="mobile-nav-link" data-turbo="false">{{ __('common.admin') }}</a>
        @endif
    </div>
    <div class="p-4 border-t border-gray-200 flex items-center justify-between gap-2 shrink-0" style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
        <span class="text-sm text-gray-500">{{ __('common.cart') }}</span>
        <div class="flex items-center gap-2">
            <x-color-mode-toggle />
            <button type="button" data-open-cart class="theme-cart-btn relative theme-icon-btn" aria-label="{{ __('common.cart') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span data-cart-count class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center {{ ($cartCount ?? 0) ? '' : 'hidden' }}">{{ $cartCount ?? 0 }}</span>
            </button>
        </div>
    </div>
</nav>
