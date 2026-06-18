@extends('layouts.admin')
@section('title', 'Menu Builder')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage → Header menu">
<h2 class="text-xl font-semibold mb-6">Menu Builder</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($locations as $location)
    @php $menu = $menus->firstWhere('location', $location->value); @endphp
    <div class="card p-6">
        <h3 class="font-semibold text-lg mb-2">{{ $location->label() }}</h3>
        <p class="text-sm text-gray-500 mb-4">{{ $menu?->allItems->count() ?? 0 }} items</p>
        @if($menu && $menu->allItems->isNotEmpty())
        <ul class="text-sm space-y-1 mb-4 text-gray-600">
            @foreach($menu->allItems->whereNull('parent_id')->take(5) as $item)
            <li>• {{ $item->title }}</li>
            @endforeach
        </ul>
        @endif
        <a href="{{ route('admin.cms.menus.edit', $location) }}" class="btn-primary inline-block">Edit Menu</a>
    </div>
    @endforeach
</div>
</x-admin.builder-layout>
@endsection
