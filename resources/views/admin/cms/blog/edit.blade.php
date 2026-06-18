@extends('layouts.admin')
@section('title', 'Edit Post')
@section('content')
@php
    $previewUrl = builder_preview_url('/blog/'.$post->slug);
@endphp
<x-admin.builder-layout :preview-url="$previewUrl" :open-url="$previewUrl" :preview-label="'Blog: /blog/'.$post->slug">
<h2 class="text-lg font-semibold mb-4">Edit Post</h2>
<form action="{{ route('admin.cms.blog.update', $post) }}" method="POST" enctype="multipart/form-data" class="card p-5 space-y-4" data-preview-slug-form data-preview-type="blog">
    @csrf @method('PUT')
    @include('admin.cms.blog._form', ['post' => $post, 'categories' => $categories])
    <button class="btn-primary mt-6">Update Post</button>
</form>
</x-admin.builder-layout>
@endsection
