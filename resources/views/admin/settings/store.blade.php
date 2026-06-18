@extends('layouts.admin')
@section('title', 'Store Settings')
@section('content')
<form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl space-y-4">
    @csrf @method('PUT')
    <div class="card p-6 space-y-4">
        <h3 class="font-semibold text-lg">Store Identity</h3>
        <div><label class="label">Store Name</label><input name="name" value="{{ $settings['name'] ?? '' }}" class="input" required></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-admin.image-uploader name="logo" label="Store Logo" :existing-url="image_url($settings['logo'] ?? '')" hint="PNG or SVG · shown in header" />
            <x-admin.image-uploader name="favicon" label="Favicon" :existing-url="image_url($settings['favicon'] ?? '')" hint="Square icon · 64×64px or larger" />
        </div>
    </div>
    <div class="card p-6 space-y-4">
        <h3 class="font-semibold text-lg">Contact & Social</h3>
        <div><label class="label">Phone</label><input name="phone" value="{{ $settings['phone'] ?? '' }}" class="input"></div>
        <div><label class="label">Email</label><input name="email" type="email" value="{{ $settings['email'] ?? '' }}" class="input"></div>
        <div><label class="label">Address</label><textarea name="address" class="input" rows="2">{{ $settings['address'] ?? '' }}</textarea></div>
        <div><label class="label">Facebook URL</label><input name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}" class="input"></div>
        <div><label class="label">Instagram URL</label><input name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}" class="input"></div>
        <div><label class="label">WhatsApp Number</label><input name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" class="input"></div>
        <div><label class="label">Messenger Link</label><input name="messenger_link" value="{{ $settings['messenger_link'] ?? '' }}" class="input"></div>
        <div><label class="label">Meta Description</label><textarea name="meta_description" class="input" rows="2">{{ $settings['meta_description'] ?? '' }}</textarea></div>
    </div>
    <button class="btn-primary">Save Settings</button>
</form>
@endsection
