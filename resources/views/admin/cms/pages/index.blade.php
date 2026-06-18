@extends('layouts.admin')
@section('title', 'CMS Pages')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage">
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h2 class="text-xl font-semibold">Pages</h2>
    <a href="{{ route('admin.cms.pages.create') }}" class="btn-primary">Create Page</a>
</div>
<form class="mb-4 flex gap-2" method="GET">
    <input name="search" value="{{ request('search') }}" class="input max-w-xs" placeholder="Search pages...">
    <select name="status" class="input max-w-[10rem]">
        <option value="">All statuses</option>
        @foreach(\App\Enums\ContentStatus::cases() as $s)
        <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
        @endforeach
    </select>
    <button class="btn-secondary">Filter</button>
</form>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-left">Slug</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pages as $page)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $page->title }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $page->slug }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $page->status->value === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $page->status->label() }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    <x-admin.action-group>
                        @if($page->status->value === 'published')
                        <x-admin.action-btn variant="external" :href="route('pages.show', $page->slug)" target="_blank" />
                        @endif
                        <x-admin.action-btn variant="edit" :href="route('admin.cms.pages.edit', $page)" />
                        <x-admin.action-btn variant="delete" :action="route('admin.cms.pages.destroy', $page)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$page->title" />
                    </x-admin.action-group>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No pages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $pages->links() }}</div>
</x-admin.builder-layout>
@endsection
