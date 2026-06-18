@extends('layouts.admin')
@section('title', 'Marketing Settings')
@section('content')
<form action="{{ route('admin.settings.marketing') }}" method="POST" class="max-w-2xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div><label class="label">Facebook Pixel ID</label><input name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] ?? '' }}" class="input"></div>
    <div><label class="label">Facebook Conversion API Token</label><input name="facebook_capi_token" value="{{ $settings['facebook_capi_token'] ?? '' }}" class="input"></div>
    <div><label class="label">Facebook Dataset ID</label><input name="facebook_dataset_id" value="{{ $settings['facebook_dataset_id'] ?? '' }}" class="input"></div>
    <div><label class="label">Google Analytics ID</label><input name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" class="input" placeholder="G-XXXXXXXX"></div>
    <div><label class="label">Google Tag Manager ID</label><input name="google_tag_manager_id" value="{{ $settings['google_tag_manager_id'] ?? '' }}" class="input" placeholder="GTM-XXXXXXX"></div>
    <div><label class="label">TikTok Pixel ID</label><input name="tiktok_pixel_id" value="{{ $settings['tiktok_pixel_id'] ?? '' }}" class="input"></div>
    <button class="btn-primary">Save Marketing Settings</button>
</form>
@endsection
