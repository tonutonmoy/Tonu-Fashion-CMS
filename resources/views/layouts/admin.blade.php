<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-prefetch" content="true">
    <title>@yield('title', 'Admin') | {{ $storeSettings['name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/admin-entry.js'])
</head>
<body class="admin-body bg-gray-100 min-h-screen theme-mode-{{ $colorMode ?? 'light' }}" data-color-mode="{{ $colorMode ?? 'light' }}" data-admin-support-notify>
    <x-admin.toast />
    <x-admin.confirm-modal />
    <div id="admin-loading" class="hidden fixed inset-0 z-[200] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm" aria-live="polite" aria-busy="true">
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 flex flex-col items-center gap-3 min-w-[12rem]">
            <div class="w-10 h-10 border-4 border-red-100 border-t-red-600 rounded-full animate-spin" role="status"></div>
            <p id="admin-loading-text" class="text-sm font-medium text-gray-700">Please wait…</p>
        </div>
    </div>
    <div class="flex min-h-screen">
        @include('partials.admin.sidebar')
        <div id="admin-main-wrap" class="flex-1 flex flex-col min-w-0">
            @include('partials.admin.header')
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
