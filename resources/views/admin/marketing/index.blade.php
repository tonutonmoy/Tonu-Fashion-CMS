@extends('layouts.admin')
@section('title', 'Marketing & Pixels')
@section('content')
@include('admin.marketing._nav')
<form action="{{ route('admin.marketing.index') }}" method="POST" class="max-w-2xl card p-6 space-y-4">
    @csrf @method('PUT')
    <h2 class="font-semibold text-lg">Facebook Pixel & CAPI</h2>
    <div><label class="label">Facebook Pixel ID</label><input name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] ?? '' }}" class="input" placeholder="1234567890"></div>
    <div><label class="label">Facebook Access Token (CAPI)</label><input name="facebook_access_token" type="password" value="{{ $settings['facebook_access_token'] ?? '' }}" class="input"></div>
    <div><label class="label">Facebook Dataset ID</label><input name="facebook_dataset_id" value="{{ $settings['facebook_dataset_id'] ?? '' }}" class="input"></div>
    <div><label class="label">Test Event Code</label><input name="test_event_code" value="{{ $settings['test_event_code'] ?? '' }}" class="input" placeholder="TEST12345"></div>

    <h2 class="font-semibold text-lg pt-4">Google & TikTok</h2>
    <div><label class="label">GA4 Measurement ID</label><input name="ga_measurement_id" value="{{ $settings['ga_measurement_id'] ?? '' }}" class="input" placeholder="G-XXXXXXXX"></div>
    <div><label class="label">Google Tag Manager ID</label><input name="gtm_id" value="{{ $settings['gtm_id'] ?? '' }}" class="input" placeholder="GTM-XXXXXXX"></div>
    <div><label class="label">TikTok Pixel ID</label><input name="tiktok_pixel_id" value="{{ $settings['tiktok_pixel_id'] ?? '' }}" class="input"></div>
    <button class="btn-primary">Save Marketing Settings</button>
</form>
@endsection
