@extends('layouts.admin')
@section('title', 'Footer Builder')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url(null, 'site-footer')" preview-label="Homepage → Footer">
<form action="{{ route('admin.theme.footer') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl card p-6 space-y-4">
    @csrf @method('PUT')
    <x-admin.image-uploader name="logo" label="Footer Logo" :existing-url="image_url($settings->logo)" hint="Optional logo in footer area" />
    <div><label class="label">Description</label><textarea name="description" class="input" rows="3">{{ $settings->description }}</textarea></div>
    <div><label class="label">Address</label><textarea name="address" class="input" rows="2">{{ $settings->address }}</textarea></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Phone</label><input name="phone" value="{{ $settings->phone }}" class="input"></div>
        <div><label class="label">Email</label><input name="email" type="email" value="{{ $settings->email }}" class="input"></div>
    </div>
    <div><label class="label">Facebook URL</label><input name="facebook_url" value="{{ $settings->facebook_url }}" class="input"></div>
    <div><label class="label">Instagram URL</label><input name="instagram_url" value="{{ $settings->instagram_url }}" class="input"></div>
    <div><label class="label">YouTube URL</label><input name="youtube_url" value="{{ $settings->youtube_url }}" class="input"></div>
    <div><label class="label">WhatsApp Number</label><input name="whatsapp_number" value="{{ $settings->whatsapp_number }}" class="input"></div>
    <div><label class="label">Messenger Link</label><input name="messenger_link" value="{{ $settings->messenger_link }}" class="input"></div>
    <div><label class="label">Copyright Text</label><input name="copyright_text" value="{{ $settings->copyright_text }}" class="input"></div>
    <button class="btn-primary">Save Draft</button>
</form>
</x-admin.builder-layout>
@endsection
