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
    @php
        $isFlashSale = $section->section_key === 'flash_sale';
        $flashCount = $isFlashSale ? ($flashSaleProductCount ?? 0) : 0;
    @endphp
    <div class="card p-6 {{ $section->enabled ? '' : 'opacity-80' }}" data-section-id="{{ $section->id }}">
        <div class="flex flex-wrap items-center gap-3 mb-4 cursor-grab">
            <span class="text-gray-400 text-lg shrink-0" title="Drag to reorder">⠿</span>

            @if($section->enabled)
                @if($isFlashSale && $flashCount > 0)
                <form action="{{ route('admin.theme.homepage.toggle', $section) }}" method="POST" class="inline"
                      data-confirm
                      data-confirm-title="Disable Flash Sale section?"
                      data-confirm-message="{{ $flashCount }} product(s) are in this flash sale. Remove the offer from all products and disable the section?"
                      data-confirm-ok="Yes, disable">
                    @csrf @method('PATCH')
                    <input type="hidden" name="enabled" value="0">
                    <input type="hidden" name="clear_flash_products" value="1">
                    <button type="submit" class="btn-secondary text-sm text-red-700 border-red-200 hover:bg-red-50">Deactivate</button>
                </form>
                @else
                <form action="{{ route('admin.theme.homepage.toggle', $section) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="enabled" value="0">
                    <button type="submit" class="btn-secondary text-sm text-red-700 border-red-200 hover:bg-red-50">Deactivate</button>
                </form>
                @endif
                <span class="badge bg-green-100 text-green-800">Active</span>
            @else
                <form action="{{ route('admin.theme.homepage.toggle', $section) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="btn-secondary text-sm text-green-800 border-green-200 hover:bg-green-50">Activate</button>
                </form>
                <span class="badge bg-gray-100 text-gray-600">Inactive</span>
            @endif

            <h3 class="font-semibold flex-1 min-w-[10rem]">{{ $section->title }}</h3>
            <span class="text-xs text-gray-400">#{{ $section->sort_order }}</span>
        </div>

        @if($isFlashSale && $section->enabled && $flashCount > 0)
        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-4">
            {{ $flashCount }} product(s) marked for flash sale. Deactivating will ask to remove the offer from them.
        </p>
        @endif

        <form action="{{ route('admin.theme.homepage.update', $section) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm {{ $section->enabled ? '' : 'pointer-events-none opacity-50' }}">
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
            <div><label class="label">Starts</label><input type="datetime-local" name="settings[start_at]" value="{{ isset($section->settings['start_at']) ? \Carbon\Carbon::parse($section->settings['start_at'])->format('Y-m-d\TH:i') : '' }}" class="input"></div>
            <div><label class="label">Ends</label><input type="datetime-local" name="settings[end_at]" value="{{ isset($section->settings['end_at']) ? \Carbon\Carbon::parse($section->settings['end_at'])->format('Y-m-d\TH:i') : '' }}" class="input"></div>
            <div><label class="label">Discount %</label><input type="number" name="settings[discount]" value="{{ $section->settings['discount'] ?? 20 }}" class="input" min="1" max="90"></div>
            <div><label class="label flex items-center gap-2 mt-6"><input type="checkbox" name="settings[show_countdown]" value="1" @checked($section->settings['show_countdown'] ?? true)> Show Countdown</label></div>
            <div class="md:col-span-2"><label class="label">Flash sale products (optional — or mark products in catalog)</label>
                <select name="settings[product_ids][]" multiple class="input h-32">
                    @foreach($products as $p)<option value="{{ $p->id }}" @selected(in_array($p->id, $section->settings['product_ids'] ?? []))>{{ $p->name }}</option>@endforeach
                </select>
            </div>
            @endif
            @if($section->section_key === 'newsletter')
            <div><label class="label">Title</label><input name="settings[title]" value="{{ $section->settings['title'] ?? '' }}" class="input"></div>
            <div><label class="label">Subtitle</label><input name="settings[subtitle]" value="{{ $section->settings['subtitle'] ?? '' }}" class="input"></div>
            @endif
            <div class="md:col-span-2 flex items-center gap-3">
                <button type="submit" class="btn-primary" @disabled(! $section->enabled)>Save Section</button>
                @unless($section->enabled)
                <span class="text-xs text-gray-500">Activate section to edit settings</span>
                @endunless
            </div>
        </form>
    </div>
    @endforeach
</div>
</x-admin.builder-layout>
@endsection
