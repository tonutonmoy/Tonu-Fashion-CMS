@extends('layouts.admin')
@section('title', $location->label())
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage → Header menu">
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold">{{ $location->label() }}</h2>
    <a href="{{ route('admin.cms.menus.index') }}" class="text-sm text-gray-500 hover:underline">← All Menus</a>
</div>

<form action="{{ route('admin.cms.menus.update', $location) }}" method="POST" class="max-w-3xl" data-menu-builder data-pages='@json($pages->map(fn($p) => ["id" => $p->id, "title" => $p->title, "slug" => $p->slug]))'>
    @csrf @method('PUT')
    <input type="hidden" name="name" value="{{ $menu?->name ?? $location->label() }}">
    <input type="hidden" name="items_json" data-menu-json value="[]">

    <div class="card p-6 mb-4">
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" class="btn-secondary text-sm" data-menu-add="link">+ Custom Link</button>
            <button type="button" class="btn-secondary text-sm" data-menu-add="page">+ Page Link</button>
            <button type="button" class="btn-secondary text-sm" data-menu-add="dropdown">+ Dropdown</button>
        </div>
        <p class="text-xs text-gray-500 mb-4">Drag items to reorder. Dropdowns can contain child links.</p>
        <ul class="space-y-2" data-menu-list></ul>
        <p class="text-sm text-gray-400 mt-4 hidden" data-menu-empty>No menu items yet. Add links above.</p>
    </div>

    <button type="submit" class="btn-primary">Save Menu</button>
</form>

<template id="menu-item-template">
    <li class="border border-gray-200 rounded-lg p-3 bg-white" data-menu-item draggable="true">
        <div class="flex items-start gap-2">
            <span class="cursor-grab text-gray-400 mt-2" title="Drag">⠿</span>
            <div class="flex-1 space-y-2">
                <input type="text" class="input text-sm" data-field="title" placeholder="Label" required>
                <div data-link-fields>
                    <select class="input text-sm mb-2 hidden" data-field="page_id">
                        <option value="">Select page</option>
                        @foreach($pages as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="input text-sm" data-field="url" placeholder="URL (e.g. /shop)">
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-500">
                    <input type="checkbox" data-field="open_in_new_tab"> Open in new tab
                </label>
                <ul class="pl-4 space-y-2 border-l-2 border-gray-100 hidden" data-children></ul>
                <button type="button" class="text-xs text-brand-600 hidden" data-add-child>+ Add dropdown item</button>
            </div>
            <button type="button" class="text-red-500 text-sm shrink-0" data-remove>Remove</button>
        </div>
    </li>
</template>

@php
$initialItems = ($menu?->items ?? collect())->map(fn($item) => [
    'title' => $item->title,
    'url' => $item->url,
    'page_id' => $item->page_id,
    'open_in_new_tab' => $item->open_in_new_tab,
    'children' => $item->children->map(fn($c) => [
        'title' => $c->title,
        'url' => $c->url,
        'page_id' => $c->page_id,
        'open_in_new_tab' => $c->open_in_new_tab,
    ])->values()->all(),
])->values()->all();
@endphp
<script type="application/json" id="menu-initial-data">@json($initialItems)</script>
</x-admin.builder-layout>
@endsection
