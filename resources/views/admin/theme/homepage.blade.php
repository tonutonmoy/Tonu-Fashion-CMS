@extends('layouts.admin')
@section('title', 'Homepage Builder')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage — all sections">

<form action="{{ route('admin.theme.homepage.reorder') }}" method="POST" class="mb-6" data-homepage-reorder-form>
    @csrf @method('PATCH')
    <button type="submit" class="btn-secondary">Save Section Order</button>
</form>

<div class="space-y-3" data-homepage-sort>
    @foreach($sections as $section)
    <div class="card p-6" data-section-id="{{ $section->id }}">
        <div class="flex items-center gap-3 mb-4 cursor-grab">
            <span class="text-gray-400 text-lg" title="Drag to reorder">⠿</span>
            <form action="{{ route('admin.theme.homepage.toggle', $section) }}" method="POST">@csrf @method('PATCH')
                <input type="hidden" name="enabled" value="{{ $section->enabled ? '0' : '1' }}">
                <button type="submit" class="badge {{ $section->enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $section->enabled ? 'Enabled' : 'Disabled' }}
                </button>
            </form>
            <h3 class="font-semibold flex-1">{{ $section->title }}</h3>
            <span class="text-xs text-gray-400">#{{ $section->sort_order }}</span>
        </div>
        <form action="{{ route('admin.theme.homepage.update', $section) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @csrf @method('PUT')
            @if(in_array($section->section_key, ['categories', 'featured_products', 'new_arrivals', 'best_sellers', 'customer_reviews']))
            <div><label class="label">Limit</label><input type="number" name="settings[limit]" value="{{ $section->settings['limit'] ?? 6 }}" class="input" min="1" max="24"></div>
            @endif
            @if($section->section_key === 'categories')
            <div class="md:col-span-2"><label class="label">Categories (leave empty for all)</label>
                <select name="settings[category_ids][]" multiple class="input h-32">
                    @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(in_array($cat->id, $section->settings['category_ids'] ?? []))>{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            @endif
            @if($section->section_key === 'featured_products')
            <div class="md:col-span-2"><label class="label">Products (leave empty for auto featured)</label>
                <select name="settings[product_ids][]" multiple class="input h-32">
                    @foreach($products as $p)<option value="{{ $p->id }}" @selected(in_array($p->id, $section->settings['product_ids'] ?? []))>{{ $p->name }}</option>@endforeach
                </select>
            </div>
            @endif
            @if($section->section_key === 'flash_sale')
            <div><label class="label">Limit</label><input type="number" name="settings[limit]" value="{{ $section->settings['limit'] ?? 8 }}" class="input" min="1" max="24"></div>
            <div><label class="label">Start Date</label><input type="date" name="settings[start_date]" value="{{ $section->settings['start_date'] ?? '' }}" class="input"></div>
            <div><label class="label">End Date</label><input type="date" name="settings[end_date]" value="{{ $section->settings['end_date'] ?? '' }}" class="input"></div>
            <div><label class="label">Discount %</label><input type="number" name="settings[discount]" value="{{ $section->settings['discount'] ?? 20 }}" class="input"></div>
            <div><label class="label flex items-center gap-2 mt-6"><input type="checkbox" name="settings[show_countdown]" value="1" @checked($section->settings['show_countdown'] ?? true)> Show Countdown</label></div>
            @endif
            @if($section->section_key === 'newsletter')
            <div><label class="label">Title</label><input name="settings[title]" value="{{ $section->settings['title'] ?? '' }}" class="input"></div>
            <div><label class="label">Subtitle</label><input name="settings[subtitle]" value="{{ $section->settings['subtitle'] ?? '' }}" class="input"></div>
            @endif
            <div class="md:col-span-2"><button class="btn-primary">Save Section</button></div>
        </form>
    </div>
    @endforeach
</div>
</x-admin.builder-layout>
@endsection
