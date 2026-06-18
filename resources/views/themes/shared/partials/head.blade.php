@props(['theme' => $activeTheme ?? 'fashion-modern'])

@if($storeSettings['favicon'] ?? null)
    <link rel="icon" href="{{ image_url($storeSettings['favicon']) }}">
@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="{{ theme()->googleFontUrl() }}" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="{{ theme()->googleFontUrl() }}" rel="stylesheet"></noscript>
<style>{!! theme()->cssVariables() !!}</style>
@vite(['resources/css/app.css', 'resources/js/storefront.js'])
<link rel="stylesheet" href="{{ theme_asset('theme.css') }}">
