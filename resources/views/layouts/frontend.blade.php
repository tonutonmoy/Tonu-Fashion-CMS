<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo :meta="$seo ?? []" />
    @include('themes.shared.partials.head')
    <x-marketing-pixels />
</head>
<body class="theme-body theme-{{ $activeTheme ?? 'fashion-modern' }} theme-mode-{{ $colorMode ?? 'light' }} min-h-screen flex flex-col" data-color-mode="{{ $colorMode ?? 'light' }}">
    @include('themes.shared.partials.body-marketing')
    @include('themes.shared.partials.header')
    <main class="theme-main flex-1">
        <x-alerts />
        @yield('content')
    </main>
    @include('themes.shared.partials.footer')
    @include('themes.shared.partials.cart-sidebar')
    @stack('scripts')
</body>
</html>
