@extends('layouts.admin')
@section('title', 'Blog Posts')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url('/blog')" preview-label="Blog listing">
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-semibold">Blog</h2>
    <a href="{{ route('admin.cms.blog.create') }}" class="btn-primary">New Post</a>
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($posts as $post)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $post->title }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $post->category?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $post->status->value === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $post->status->label() }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    <x-admin.action-group>
                        @if($post->status->value === 'published')
                        <x-admin.action-btn variant="external" :href="route('blog.show', $post->slug)" target="_blank" />
                        @endif
                        <x-admin.action-btn variant="edit" :href="route('admin.cms.blog.edit', $post)" />
                        <x-admin.action-btn variant="delete" :action="route('admin.cms.blog.destroy', $post)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete').' '.$post->title" />
                    </x-admin.action-group>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $posts->links() }}</div>
</x-admin.builder-layout>
@endsection
