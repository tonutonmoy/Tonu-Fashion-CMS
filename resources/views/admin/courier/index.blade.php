@extends('layouts.admin')
@section('title', 'Courier Integration')
@section('content')
<div class="mb-6 flex flex-wrap gap-4 border-b border-gray-200 pb-4 text-sm">
    <a href="{{ route('admin.courier.index') }}" class="font-semibold text-brand-600">Courier Settings</a>
    <a href="{{ route('admin.courier.activity') }}" class="text-gray-600 hover:text-brand-600">Activity Logs</a>
</div>

<form action="{{ route('admin.courier.index') }}" method="POST" class="max-w-3xl space-y-8">
    @csrf @method('PUT')

    <div class="card p-6 space-y-4">
        <h2 class="font-semibold text-lg">Automation</h2>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="auto_parcel_enabled" value="1" @checked($settings['auto_parcel_enabled'] ?? false)>
            Enable Auto Parcel — create parcel when order is confirmed
        </label>
        <div>
            <label class="label">Default Courier</label>
            <select name="default_courier" class="input">
                @foreach(['steadfast' => 'Steadfast', 'pathao' => 'Pathao Courier', 'redx' => 'RedX'] as $value => $label)
                    <option value="{{ $value }}" @selected(($settings['default_courier'] ?? 'steadfast') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @foreach(['steadfast' => 'Steadfast', 'pathao' => 'Pathao Courier', 'redx' => 'RedX'] as $key => $label)
    @php $c = $settings[$key] ?? [] @endphp
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg">{{ $label }}</h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="{{ $key }}_enabled" value="1" @checked($c['enabled'] ?? false)> Enable
            </label>
        </div>
        <div><label class="label">API Key</label><input name="{{ $key }}_api_key" value="{{ $c['api_key'] ?? '' }}" class="input" type="password"></div>
        <div><label class="label">Secret Key</label><input name="{{ $key }}_secret_key" value="{{ $c['secret_key'] ?? '' }}" class="input" type="password"></div>
        <div><label class="label">Base URL</label><input name="{{ $key }}_base_url" value="{{ $c['base_url'] ?? config('couriers.'.$key.'.default_base_url') }}" class="input"></div>
    </div>
    @endforeach

    <button class="btn-primary">Save Courier Settings</button>
</form>
@endsection
