@extends('layouts.admin')
@section('title', 'Edit Page')
@section('content')
@php
    $previewUrl = builder_preview_url('/pages/'.$page->slug, null, 'desktop');
@endphp
<x-admin.builder-layout :preview-url="$previewUrl" :open-url="$previewUrl" :preview-label="'Page: /pages/'.$page->slug">
<h2 class="text-lg font-semibold mb-4">Edit Page</h2>
<form action="{{ route('admin.cms.pages.update', $page) }}" method="POST" enctype="multipart/form-data" class="card p-5 space-y-4" data-preview-slug-form data-preview-type="page">
    @csrf @method('PUT')
    @include('admin.cms.pages._form', ['page' => $page])
    <button class="btn-primary mt-6">Update Page</button>
</form>
</x-admin.builder-layout>
@endsection
