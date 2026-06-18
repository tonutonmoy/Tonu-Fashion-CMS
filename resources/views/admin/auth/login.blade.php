<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md card p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Admin Login</h1>
        @if($errors->any())
            <div class="bg-red-50 text-red-800 p-3 rounded-lg mb-4 text-sm">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label">Email / Username</label>
                <input type="text" name="email" class="input" value="{{ old('email', config('admin.email')) }}" required autofocus>
            </div>
            <div>
                <label class="label">Password</label>
                <input type="password" name="password" class="input" required>
            </div>
            <button type="submit" class="btn-primary w-full">Login</button>
        </form>
    </div>
</body>
</html>
