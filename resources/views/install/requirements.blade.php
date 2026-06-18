@extends('install.layout')
@section('title', 'System Requirements')
@section('step', 1)
@section('content')
<h2 class="text-2xl font-bold mb-2">System Requirements</h2>
<p class="text-gray-600 mb-6">Verify your server meets the minimum requirements for Fashion BD.</p>

<div class="space-y-3 mb-8">
    @foreach($checks as $check)
    <div class="flex items-center justify-between p-4 rounded-xl border {{ $check['passed'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
        <div>
            <p class="font-medium">{{ $check['label'] }}</p>
            @if($check['detail'])
                <p class="text-xs text-gray-500 mt-1">{{ $check['detail'] }}</p>
            @endif
        </div>
        <span class="text-sm font-semibold {{ $check['passed'] ? 'text-green-700' : 'text-red-700' }}">
            {{ $check['passed'] ? 'Pass' : 'Fail' }}
        </span>
    </div>
    @endforeach
</div>

<div class="flex justify-end">
    @if($passed)
        <a href="{{ route('install.database') }}" class="btn-primary">Next: Database →</a>
    @else
        <button type="button" disabled class="btn-primary opacity-50 cursor-not-allowed">Fix requirements to continue</button>
    @endif
</div>
@endsection
