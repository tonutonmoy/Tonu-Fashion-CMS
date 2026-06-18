@extends('layouts.admin')
@section('title', 'Payment Gateways')
@section('content')
<form action="{{ route('admin.payment.index') }}" method="POST" class="max-w-3xl space-y-8">
    @csrf @method('PUT')

    <div class="card p-6">
        <label class="flex items-center gap-2 font-semibold">
            <input type="checkbox" name="cod_enabled" value="1" @checked($settings['cod_enabled'] ?? true)>
            Cash on Delivery (COD)
        </label>
    </div>

    @foreach(['bkash' => 'bKash', 'nagad' => 'Nagad', 'sslcommerz' => 'SSLCommerz'] as $key => $label)
    @php $g = $settings[$key] ?? [] @endphp
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg">{{ $label }}</h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="{{ $key }}_enabled" value="1" @checked($g['enabled'] ?? false)> Enable
            </label>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="{{ $key }}_sandbox" value="1" @checked($g['sandbox'] ?? true)> Sandbox mode
        </label>
        @if($key === 'sslcommerz')
        <div><label class="label">Store ID</label><input name="{{ $key }}_store_id" value="{{ $g['store_id'] ?? '' }}" class="input"></div>
        <div><label class="label">Store Password</label><input name="{{ $key }}_app_secret" type="password" value="{{ $g['app_secret'] ?? '' }}" class="input"></div>
        @elseif($key === 'nagad')
        <div><label class="label">Merchant ID</label><input name="{{ $key }}_merchant_id" value="{{ $g['merchant_id'] ?? '' }}" class="input"></div>
        <div><label class="label">Public Key (base64)</label><input name="{{ $key }}_app_key" value="{{ $g['app_key'] ?? '' }}" class="input"></div>
        <div><label class="label">Private Key (base64)</label><input name="{{ $key }}_app_secret" type="password" value="{{ $g['app_secret'] ?? '' }}" class="input"></div>
        @else
        <div><label class="label">App Key</label><input name="{{ $key }}_app_key" value="{{ $g['app_key'] ?? '' }}" class="input"></div>
        <div><label class="label">App Secret</label><input name="{{ $key }}_app_secret" type="password" value="{{ $g['app_secret'] ?? '' }}" class="input"></div>
        <div><label class="label">Username</label><input name="{{ $key }}_username" value="{{ $g['username'] ?? '' }}" class="input"></div>
        <div><label class="label">Password</label><input name="{{ $key }}_password" type="password" value="{{ $g['password'] ?? '' }}" class="input"></div>
        @endif
        <div><label class="label">Base URL (optional override)</label><input name="{{ $key }}_base_url" value="{{ $g['base_url'] ?? '' }}" class="input" placeholder="{{ config('payments.'.$key.'.sandbox_url') }}"></div>
    </div>
    @endforeach

    <button class="btn-primary">Save Payment Settings</button>
</form>
@endsection
