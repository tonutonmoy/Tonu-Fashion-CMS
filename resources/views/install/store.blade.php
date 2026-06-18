@extends('install.layout')
@section('title', 'Store Information')
@section('step', 3)
@section('content')
<h2 class="text-2xl font-bold mb-2">Store Information</h2>
<p class="text-gray-600 mb-6">Configure your fashion store branding and defaults.</p>

<form action="{{ route('install.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="label">Store Name</label>
        <input name="store_name" value="{{ old('store_name', $data['store_name']) }}" class="input w-full" required>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Store Email</label>
            <input type="email" name="store_email" value="{{ old('store_email', $data['store_email']) }}" class="input w-full" required>
        </div>
        <div>
            <label class="label">Phone</label>
            <input name="phone" value="{{ old('phone', $data['phone']) }}" class="input w-full" required>
        </div>
    </div>
    <div>
        <label class="label">Address</label>
        <textarea name="address" rows="2" class="input w-full" required>{{ old('address', $data['address']) }}</textarea>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Currency</label>
            <select name="currency_code" class="input w-full" id="currency_code">
                @foreach($currencies as $code => $currency)
                    <option value="{{ $code }}" data-symbol="{{ $currency['symbol'] }}" @selected(old('currency_code', $data['currency_code']) === $code)>{{ $currency['label'] }}</option>
                @endforeach
            </select>
            <input type="hidden" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $data['currency_symbol']) }}">
        </div>
        <div>
            <label class="label">Timezone</label>
            <select name="timezone" class="input w-full">
                @foreach($timezones as $value => $label)
                    <option value="{{ $value }}" @selected(old('timezone', $data['timezone']) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="label">Default Theme</label>
        <select name="default_theme" class="input w-full">
            @foreach($themes as $slug => $theme)
                <option value="{{ $slug }}" @selected(old('default_theme', $data['default_theme']) === $slug)>{{ $theme['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Application URL</label>
        <input type="url" name="app_url" value="{{ old('app_url', $data['app_url']) }}" class="input w-full" placeholder="https://yourdomain.com">
    </div>
    <div class="flex justify-between pt-4">
        <a href="{{ route('install.database') }}" class="btn-secondary">← Previous</a>
        <button type="submit" class="btn-primary">Next: Admin Account →</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('currency_code')?.addEventListener('change', function () {
    const symbol = this.options[this.selectedIndex].dataset.symbol;
    document.getElementById('currency_symbol').value = symbol;
});
</script>
@endpush
