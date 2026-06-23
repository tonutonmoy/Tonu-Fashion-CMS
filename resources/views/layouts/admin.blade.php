<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-prefetch" content="false">
    <meta name="turbo-cache-control" content="no-cache">
    <title>@yield('title', 'Admin') | {{ $storeSettings['name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/admin-entry.js'])
</head>
<body class="admin-body bg-gray-100 min-h-screen theme-mode-{{ $colorMode ?? 'light' }}" data-color-mode="{{ $colorMode ?? 'light' }}" data-admin-support-notify>
    <x-admin.toast />
    <x-admin.confirm-modal />
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
