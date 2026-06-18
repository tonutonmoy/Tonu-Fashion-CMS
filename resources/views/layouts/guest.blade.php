<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Account') | {{ $storeSettings['name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/storefront.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    @include('partials.frontend.header')
    <main class="flex-1 flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">
            <x-alerts />
            @yield('content')
        </div>
    </main>
    @include('partials.frontend.footer')
</body>
</html>
