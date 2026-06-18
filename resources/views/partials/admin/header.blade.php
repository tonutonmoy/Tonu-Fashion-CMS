<header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <button type="button" id="admin-sidebar-toggle" class="admin-sidebar-toggle-btn theme-icon-btn shrink-0" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-base sm:text-lg font-semibold text-gray-900 truncate">@yield('title', 'Dashboard')</h1>
    </div>
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <x-color-mode-toggle />
        <a href="{{ route('home') }}" target="_blank" class="text-xs sm:text-sm text-gray-600 hover:text-gray-900 hidden sm:inline">{{ __('admin.view_store') }}</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-700">{{ __('common.logout') }}</button></form>
    </div>
</header>
