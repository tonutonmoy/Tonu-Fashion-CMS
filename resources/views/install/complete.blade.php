@extends('install.layout')
@section('title', 'Installation Complete')
@section('content')
<div class="text-center py-4">
    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center text-3xl">✓</div>
    <h2 class="text-2xl font-bold mb-2">Installation Complete!</h2>
    <p class="text-gray-600 mb-6">Fashion BD is ready. You can now log in to the admin panel.</p>

    @if(!empty($log))
    <div class="text-left bg-gray-50 border rounded-xl p-4 mb-6 text-sm space-y-1">
        @foreach($log as $line)
            <p class="text-gray-700">• {{ $line }}</p>
        @endforeach
    </div>
    @endif

    <a href="{{ route('admin.login') }}" class="btn-primary inline-block">Go to Admin Login →</a>
</div>
@endsection
