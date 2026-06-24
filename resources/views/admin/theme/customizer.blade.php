@extends('layouts.admin')
@section('title', 'Colors & Theme')
@section('content')
@php
    $previewUrl = builder_preview_url(null, null, 'desktop', $settings->active_theme);
@endphp
<div class="flex flex-wrap gap-2 mb-4" data-theme-reset-toolbar>
    <form id="theme-reset-colors" action="{{ route('admin.theme.customizer.reset') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="type" value="colors">
    </form>
    <form id="theme-reset-theme" action="{{ route('admin.theme.customizer.reset') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="type" value="theme">
    </form>
    <form id="theme-reset-all" action="{{ route('admin.theme.customizer.reset') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="type" value="all">
    </form>
    <button type="submit" form="theme-reset-colors" class="btn-secondary text-sm"
            onclick="return confirm('Reset colors to this theme defaults?')">Reset Colors</button>
    <button type="submit" form="theme-reset-theme" class="btn-secondary text-sm"
            onclick="return confirm('Reset layout & colors for current theme?')">Reset Theme</button>
    <button type="submit" form="theme-reset-all" class="btn-secondary text-sm text-red-600"
            onclick="return confirm('Restore everything to Fashion Modern defaults? Logo will be removed.')">Restore Defaults</button>
</div>

<x-admin.builder-layout :live="true" :settings="$settings" :preview-url="$previewUrl" preview-label="Live theme preview">
    @if($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold mb-1">Could not save theme settings</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.theme.customizer') }}" method="POST" enctype="multipart/form-data" data-theme-customizer data-max-file-mb="1.8" class="space-y-4">
        @csrf

        <div class="card p-5 space-y-4">
            <div>
                <h2 class="font-semibold">Choose Theme</h2>
                <p class="text-sm text-gray-500">Click a theme — preview updates instantly. Save to publish.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" data-theme-picker>
                @foreach($themes as $slug => $theme)
                @php $def = $theme['defaults'] ?? []; @endphp
                <label class="theme-pick-card border rounded-xl p-3 cursor-pointer transition {{ $settings->active_theme === $slug ? 'border-brand-600 ring-2 ring-brand-600 bg-brand-50/30' : 'border-gray-200 hover:border-gray-300' }}"
                       data-theme-slug="{{ $slug }}"
                       data-primary="{{ $def['primary_color'] ?? '#dc2626' }}"
                       data-secondary="{{ $def['secondary_color'] ?? '#1e293b' }}"
                       data-accent="{{ $def['accent_color'] ?? '#f97316' }}">
                    <input type="radio" name="active_theme" value="{{ $slug }}" class="sr-only" @checked($settings->active_theme === $slug)>
                    <div class="h-14 rounded-lg mb-2 flex items-center justify-center text-white text-sm font-semibold theme-pick-gradient"
                         style="background: linear-gradient(135deg, {{ $def['primary_color'] ?? '#dc2626' }} 0%, {{ $def['accent_color'] ?? '#f97316' }} 50%, {{ $def['secondary_color'] ?? '#1e293b' }} 100%)">
                        {{ $theme['name'] }}
                    </div>
                    <p class="text-sm font-medium">{{ $theme['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $theme['description'] }}</p>
                </label>
                @endforeach
            </div>
        </div>

        <div class="card p-5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold">Brand Colors</h2>
                    <p class="text-sm text-gray-500">3 colors only — professional look.</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <label class="text-center">
                    <span class="label block text-center mb-1">Primary</span>
                    <input type="color" name="primary_color" value="{{ $settings->primary_color }}" class="w-full h-12 rounded-lg cursor-pointer border-0" data-preview-color>
                </label>
                <label class="text-center">
                    <span class="label block text-center mb-1">Secondary</span>
                    <input type="color" name="secondary_color" value="{{ $settings->secondary_color }}" class="w-full h-12 rounded-lg cursor-pointer border-0" data-preview-color>
                </label>
                <label class="text-center">
                    <span class="label block text-center mb-1">Accent</span>
                    <input type="color" name="accent_color" value="{{ $settings->accent_color ?? '#f59e0b' }}" class="w-full h-12 rounded-lg cursor-pointer border-0" data-preview-color>
                </label>
            </div>

            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50/60 p-4 space-y-4">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Upload image to apply theme colors</p>
                    <p class="text-xs text-gray-500 mt-1">Upload a logo, banner, or brand photo — colors will be extracted automatically. Click a swatch to preview, then apply to your theme.</p>
                </div>
                <x-admin.image-uploader
                    label=""
                    :palette-only="true"
                    accept="image/png,image/jpeg,image/jpg,image/gif,image/webp"
                    hint="PNG, JPG, or WebP · not saved as logo"
                    :compact="true"
                    button-text="Choose image"
                />
                <div id="theme-image-palette" class="hidden rounded-lg border border-gray-200 bg-white p-4 space-y-3" data-image-palette-root>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Extracted colors</p>
                        <p class="text-xs text-gray-500">Click a swatch to set Primary, or apply the full palette below.</p>
                    </div>
                    <div class="flex flex-wrap gap-2" data-image-palette-swatches></div>
                    <button type="button" class="btn-primary text-sm" data-apply-palette-theme>Apply palette to theme</button>
                </div>
            </div>
        </div>

        <div class="card p-5 space-y-4">
            <h2 class="font-semibold">Logo & Font</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-admin.image-uploader name="logo" label="Store Logo" :existing-url="image_url($settings->logo)" accept="image/png,image/jpeg,image/jpg,image/gif,image/webp,image/svg+xml,.svg" hint="Shown in site header · PNG, JPG, WebP or SVG" />
                <x-admin.image-uploader name="favicon" label="Favicon" :existing-url="image_url($settings->favicon)" accept="image/png,image/jpeg,image/jpg,image/gif,image/webp,image/svg+xml,image/x-icon,.ico" hint="Browser tab icon · PNG, ICO or square image" />
            </div>
            <div>
                <label class="label">Font</label>
                <select name="font_family" class="input" data-preview-font>
                    @foreach($fonts as $name => $slug)
                    <option value="{{ $name }}" @selected($settings->font_family === $name)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card p-5 space-y-4">
            <h2 class="font-semibold">Layout Style</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Header</label>
                    <select name="header_style" class="input" data-preview-layout>@foreach($headerStyles as $k => $v)<option value="{{ $k }}" @selected($settings->header_style === $k)>{{ $v }}</option>@endforeach</select>
                </div>
                <div>
                    <label class="label">Footer</label>
                    <select name="footer_style" class="input" data-preview-layout>@foreach($footerStyles as $k => $v)<option value="{{ $k }}" @selected($settings->footer_style === $k)>{{ $v }}</option>@endforeach</select>
                </div>
                <div>
                    <label class="label">Button Shape</label>
                    <select name="button_radius" class="input" data-preview-layout>
                        @foreach(['0' => 'Square', '0.25rem' => 'Slightly Round', '0.5rem' => 'Round', '9999px' => 'Pill'] as $val => $label)
                        <option value="{{ $val }}" @selected(($settings->button_radius ?? '0.5rem') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Page Width</label>
                    <select name="container_width" class="input" data-preview-layout>
                        @foreach(['64rem' => 'Narrow (1024px)', '72rem' => 'Normal (1152px)', '80rem' => 'Wide (1280px)', '96rem' => 'Extra Wide (1536px)'] as $val => $label)
                        <option value="{{ $val }}" @selected(($settings->container_width ?? '80rem') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="btn-primary">Save Draft</button>
            <p class="text-sm text-gray-500">Live preview on the right updates as you edit.</p>
        </div>
    </form>

    <script type="application/json" id="theme-defaults-data">@json($themeDefaults)</script>
    <script type="application/json" id="theme-font-slugs">@json($fonts)</script>
</x-admin.builder-layout>
@endsection
