<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Install') | Fashion BD</title>
    @vite(['resources/css/app.css', 'resources/js/storefront.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-rose-950 min-h-screen text-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-white/10 bg-black/20 backdrop-blur">
            <div class="max-w-4xl mx-auto px-4 py-5 flex items-center justify-between">
                <div>
                    <p class="text-rose-400 text-sm font-semibold tracking-wide uppercase">Fashion BD</p>
                    <h1 class="text-xl font-bold text-white">Installation Wizard</h1>
                </div>
                <span class="text-xs text-gray-400 hidden sm:block">Shared Hosting Ready</span>
            </div>
        </header>

        <main class="flex-1 py-8 px-4">
            <div class="max-w-4xl mx-auto">
                @hasSection('step')
                    @include('install.partials.progress', ['current' => $__env->yieldContent('step')])
                @endif

                <div class="mt-8 bg-white/95 text-gray-900 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="p-6 sm:p-8">
                        @if(session('success'))
                            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
                        @endif
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
