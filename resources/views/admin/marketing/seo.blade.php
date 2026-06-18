@extends('layouts.admin')
@section('title', 'SEO Settings')
@section('content')
@include('admin.marketing._nav')
<form action="{{ route('admin.marketing.seo') }}" method="POST" class="max-w-2xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div><label class="label">Default Meta Title</label><input name="default_meta_title" value="{{ $settings['default_meta_title'] ?? '' }}" class="input"></div>
    <div><label class="label">Default Meta Description</label><textarea name="default_meta_description" class="input" rows="2">{{ $settings['default_meta_description'] ?? '' }}</textarea></div>
    <div><label class="label">Twitter Handle</label><input name="twitter_handle" value="{{ $settings['twitter_handle'] ?? '' }}" class="input" placeholder="@yourstore"></div>
    <div><label class="label">robots.txt (use {sitemap} for sitemap URL)</label>
        <textarea name="robots_txt" class="input font-mono text-xs" rows="8">{{ $settings['robots_txt'] ?? "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /checkout\nSitemap: {sitemap}" }}</textarea>
    </div>
    <button class="btn-primary">Save SEO</button>
</form>
@endsection
