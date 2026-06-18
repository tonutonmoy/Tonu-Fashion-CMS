@extends('layouts.admin')
@section('title', 'Media Library')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage">
<div class="flex flex-col gap-6 mb-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <h2 class="text-xl font-semibold">Media Library</h2>
            <p class="text-sm text-gray-500 mt-1">Upload once, reuse across products, pages, and blog.</p>
        </div>
    </div>

    <form action="{{ route('admin.cms.media.store') }}" method="POST" enctype="multipart/form-data" class="card p-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="label">Folder</label>
                <select name="folder" class="input">
                    @foreach($folders as $folder)
                    <option value="{{ $folder }}" @selected(request('folder') === $folder)>{{ $folder }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <x-admin.file-uploader
                    name="file"
                    label="Upload file"
                    accept="image/*,.webp,.svg,application/pdf"
                    :required="true"
                    hint="Max 16MB · images, WebP, SVG, or PDF"
                    button-text="Select file"
                />
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button class="btn-primary">Upload to library</button>
        </div>
    </form>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input name="q" value="{{ request('q') }}" class="input max-w-xs" placeholder="Search files...">
    <select name="folder" class="input max-w-[10rem]">
        <option value="">All folders</option>
        @foreach($folders as $folder)
        <option value="{{ $folder }}" @selected(request('folder') === $folder)>{{ $folder }}</option>
        @endforeach
    </select>
    <button class="btn-secondary">Filter</button>
</form>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @forelse($media as $item)
    <div class="card p-2 group relative overflow-hidden">
        @if(str_starts_with($item->mime_type, 'image/'))
        <img src="{{ $item->url }}" alt="{{ $item->alt ?? $item->filename }}" class="w-full h-28 object-cover rounded-lg" loading="lazy">
        @else
        <div class="w-full h-28 flex items-center justify-center bg-gray-100 rounded-lg text-xs font-semibold text-gray-500 uppercase">{{ pathinfo($item->filename, PATHINFO_EXTENSION) }}</div>
        @endif
        <p class="text-xs text-gray-600 mt-2 truncate font-medium" title="{{ $item->filename }}">{{ $item->filename }}</p>
        <p class="text-xs text-gray-400">{{ $item->folder }}</p>
        <div class="mt-2 flex gap-2 opacity-0 group-hover:opacity-100 transition">
            <button type="button" class="text-xs text-brand-600 font-medium" data-copy-url="{{ $item->url }}">Copy URL</button>
            <x-admin.action-btn variant="delete" :action="route('admin.cms.media.destroy', $item)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$item->filename" />
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="admin-uploader-drop pointer-events-none opacity-80">
            <div class="admin-uploader-drop-inner">
                <div class="admin-uploader-icon admin-uploader-icon--file mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <p class="admin-uploader-title">No media files yet</p>
                <p class="admin-uploader-subtitle">Upload images, WebP, SVG, or PDF using the form above</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $media->links() }}</div>
</x-admin.builder-layout>
@endsection
