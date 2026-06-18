@extends('layouts.admin')
@section('title', 'Theme SEO')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage">
<form action="{{ route('admin.theme.seo') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div><label class="label">Meta Title</label><input name="meta_title" value="{{ $settings->meta_title }}" class="input"></div>
    <div><label class="label">Meta Description</label><textarea name="meta_description" class="input" rows="3">{{ $settings->meta_description }}</textarea></div>
    <x-admin.image-uploader
        name="og_image"
        label="Open Graph Image"
        :existing-url="image_url($settings->og_image)"
        hint="1200×630px recommended · used when sharing on Facebook, WhatsApp, etc."
    />
    <div><label class="label">JSON-LD Schema (optional custom JSON)</label>
        <textarea name="json_ld_schema" class="input font-mono text-xs" rows="8" placeholder='Leave empty for auto-generated schema'>{{ $settings->json_ld_schema ? json_encode($settings->json_ld_schema, JSON_PRETTY_PRINT) : '' }}</textarea>
    </div>
    <button class="btn-primary">Save SEO Settings</button>
</form>
</x-admin.builder-layout>
@endsection
