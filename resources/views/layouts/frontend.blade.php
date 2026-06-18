<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo :meta="$seo ?? []" />
    @if($storeSettings['favicon'] ?? null)
        <link rel="icon" href="{{ image_url($storeSettings['favicon']) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    @vite(['resources/css/app.css', 'resources/js/storefront.js'])
    <x-marketing-pixels />
</head>
<body class="min-h-screen flex flex-col">
    @include('themes.shared.partials.body-marketing')
    @include('partials.frontend.header')
    <main class="flex-1">
        <x-alerts />
        @yield('content')
    </main>
    @include('partials.frontend.footer')
    @include('themes.shared.partials.cart-sidebar')
    @stack('scripts')
</body>
</html>
