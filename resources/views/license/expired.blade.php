<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>License Expired | Fashion BD</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 sm:p-10">
            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-orange-100 flex items-center justify-center text-3xl">⏳</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">License Expired</h1>
            <p class="text-gray-600 mb-4">{{ $message }}</p>
            @if($license)
            <p class="text-sm text-gray-500 mb-2">License: <span class="font-mono">{{ $license->license_key }}</span></p>
            <p class="text-sm text-gray-500 mb-6">Expired: {{ $license->expires_at?->format('d M Y') ?? 'N/A' }}</p>
            @endif
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm mb-6">
                <p class="font-semibold text-amber-900">Renew your license</p>
                <p class="text-amber-800 mt-1">Contact {{ $provider['provider_name'] ?? 'Fashion BD' }} to renew and restore access.</p>
                @if(!empty($provider['provider_email']))
                <p class="mt-2"><a href="mailto:{{ $provider['provider_email'] }}" class="text-brand-600 hover:underline">{{ $provider['provider_email'] }}</a></p>
                @endif
            </div>
            <a href="{{ route('admin.login') }}" class="btn-primary inline-block">Admin Login</a>
        </div>
    </div>
</body>
</html>
