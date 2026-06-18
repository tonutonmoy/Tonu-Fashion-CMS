<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>License Invalid | Fashion BD</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 sm:p-10">
            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-red-100 flex items-center justify-center text-3xl">🔒</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">License Invalid</h1>
            <p class="text-gray-600 mb-4">{{ $message }}</p>
            <p class="text-sm text-gray-500 mb-6">Current domain: <strong class="text-gray-800">{{ $domain }}</strong></p>
            <div class="rounded-xl bg-gray-50 border p-4 text-sm text-left space-y-2 mb-6">
                <p class="font-semibold text-gray-800">Contact your provider</p>
                <p>{{ $provider['provider_name'] ?? 'Fashion BD' }}</p>
                @if(!empty($provider['provider_email']))
                <p><a href="mailto:{{ $provider['provider_email'] }}" class="text-brand-600 hover:underline">{{ $provider['provider_email'] }}</a></p>
                @endif
            </div>
            <a href="{{ route('admin.login') }}" class="btn-primary inline-block">Admin Login</a>
        </div>
    </div>
</body>
</html>
