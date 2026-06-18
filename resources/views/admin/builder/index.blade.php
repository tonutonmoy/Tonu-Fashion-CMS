@extends('layouts.admin')
@section('title', 'Website Builder')
@section('content')
@php $user = auth()->user(); @endphp
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @if($user?->canAdmin('settings'))
        <a href="{{ route('admin.theme.customizer') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="theme" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Colors & Theme</h3>
            <p class="text-sm text-gray-500">Logo, 3 brand colors, fonts, header & footer style</p>
        </a>
        <a href="{{ route('admin.theme.homepage') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="homepage" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Homepage</h3>
            <p class="text-sm text-gray-500">Show/hide sections, drag to reorder</p>
        </a>
        <a href="{{ route('admin.theme.hero-slides') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="hero" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Hero Banner</h3>
            <p class="text-sm text-gray-500">Main slider images, text & buttons</p>
        </a>
        <a href="{{ route('admin.theme.footer') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="footer" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Footer</h3>
            <p class="text-sm text-gray-500">Contact info, social links, copyright</p>
        </a>
        <a href="{{ route('admin.theme.seo') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="seo" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">SEO</h3>
            <p class="text-sm text-gray-500">Google title, description & sharing image</p>
        </a>
        @endif

        @if($user?->canAdmin('cms'))
        <a href="{{ route('admin.cms.menus.index') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="menu" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Menus</h3>
            <p class="text-sm text-gray-500">Header & footer navigation links</p>
        </a>
        <a href="{{ route('admin.cms.pages.index') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="pages" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Pages</h3>
            <p class="text-sm text-gray-500">About, Contact, custom pages</p>
        </a>
        <a href="{{ route('admin.cms.media.index') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="media" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Media Library</h3>
            <p class="text-sm text-gray-500">Upload & reuse images and files</p>
        </a>
        @endif

        @if($user?->canAdmin('blog'))
        <a href="{{ route('admin.cms.blog.index') }}" class="builder-card group">
            <span class="builder-card-icon"><x-admin.icon name="blog" class="w-6 h-6" /></span>
            <h3 class="font-semibold group-hover:text-brand-600">Blog</h3>
            <p class="text-sm text-gray-500">Articles, categories & tags</p>
        </a>
        @endif
    </div>

    <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-900">
        <strong>Tip for clients:</strong> Design changes only affect how the store looks. Products, orders, and customer data are never changed from here.
    </div>

    @if($user?->canAdmin('settings'))
    <div class="mt-4 flex flex-wrap gap-3 text-sm text-gray-600">
        <span>Active theme: <strong>{{ $settings->active_theme }}</strong></span>
        <span class="inline-flex items-center gap-1">
            Colors:
            <span class="w-4 h-4 rounded-full border" style="background:{{ $settings->primary_color }}"></span>
            <span class="w-4 h-4 rounded-full border" style="background:{{ $settings->secondary_color }}"></span>
            <span class="w-4 h-4 rounded-full border" style="background:{{ $settings->accent_color ?? '#f59e0b' }}"></span>
        </span>
    </div>
    @endif
</x-admin.builder-layout>
@endsection
