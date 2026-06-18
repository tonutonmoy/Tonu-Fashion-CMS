@extends('layouts.admin')
@section('title', 'New Blog Post')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url('/blog')" preview-label="Blog listing">
<h2 class="text-lg font-semibold mb-4">New Post</h2>
<form action="{{ route('admin.cms.blog.store') }}" method="POST" enctype="multipart/form-data" class="card p-5 space-y-4" data-preview-slug-form data-preview-type="blog">
    @csrf
    @include('admin.cms.blog._form', ['categories' => $categories])
    <button class="btn-primary mt-6">Publish Post</button>
</form>
</x-admin.builder-layout>
@endsection
