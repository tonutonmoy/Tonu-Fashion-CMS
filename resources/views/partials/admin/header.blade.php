<header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <button type="button" id="admin-sidebar-toggle" class="admin-sidebar-toggle-btn theme-icon-btn shrink-0" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-base sm:text-lg font-semibold text-gray-900 truncate">@yield('title', 'Dashboard')</h1>
    </div>
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        @if(auth()->user()?->canAdmin('store'))
        <div class="relative" data-admin-low-stock>
            <button type="button" class="relative theme-icon-btn" aria-label="Low stock alerts" data-admin-low-stock-toggle>
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span data-admin-low-stock-count class="absolute -top-1 -right-1 min-w-[1.125rem] h-[1.125rem] px-1 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center {{ ($lowStockCount ?? 0) > 0 ? '' : 'hidden' }}">{{ ($lowStockCount ?? 0) > 9 ? '9+' : ($lowStockCount ?? 0) }}</span>
            </button>
            <div class="hidden absolute right-0 mt-2 w-72 sm:w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50" data-admin-low-stock-panel>
                <div class="px-4 py-3 border-b border-gray-100 font-semibold text-sm flex items-center justify-between gap-2">
                    <span>Low Stock (&lt; {{ $lowStockThreshold ?? 10 }})</span>
                    <button type="button" class="text-xs text-brand-600 font-medium" data-admin-low-stock-mark-read>Mark read</button>
                </div>
                <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 text-sm" data-admin-low-stock-list>
                    <p class="px-4 py-6 text-gray-500" data-admin-low-stock-placeholder>Open to load alerts…</p>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 text-right">
                    <a href="{{ route('admin.inventory.index', ['low_stock' => 1]) }}" class="text-xs text-brand-600 font-medium">View inventory</a>
                </div>
            </div>
        </div>
        @endif
        <x-color-mode-toggle />
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-600 hover:text-gray-900 font-medium px-2 py-1 rounded-lg hover:bg-gray-100" data-turbo="false">{{ __('admin.view_store') }}</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-700">{{ __('common.logout') }}</button></form>
    </div>
</header>
