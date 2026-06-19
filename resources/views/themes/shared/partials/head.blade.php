@props(['theme' => $activeTheme ?? 'fashion-modern'])

@if($storeSettings['favicon'] ?? null)
    <link rel="icon" href="{{ image_url($storeSettings['favicon']) }}">
@endif
<meta name="turbo-prefetch" content="false">
@if(! config('performance.use_system_fonts'))
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="{{ theme()->googleFontUrl() }}" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="{{ theme()->googleFontUrl() }}" rel="stylesheet"></noscript>
@endif
<style>
    {!! theme()->cssVariables() !!}
</style>
@vite(['resources/css/app.css', 'resources/js/storefront.js'])
<link rel="stylesheet" href="{{ theme_asset('theme.css') }}">
@vite(['resources/css/storefront-header-mobile.css'])
