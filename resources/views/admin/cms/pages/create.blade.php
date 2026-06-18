@extends('layouts.admin')
@section('title', 'Create Page')
@section('content')
<x-admin.builder-layout :preview-url="builder_preview_url()" preview-label="Homepage (new page — type slug to preview)">
<h2 class="text-lg font-semibold mb-4">Create Page</h2>
<form action="{{ route('admin.cms.pages.store') }}" method="POST" enctype="multipart/form-data" class="card p-5 space-y-4" data-preview-slug-form data-preview-type="page">
    @csrf
    @include('admin.cms.pages._form')
    <button class="btn-primary mt-6">Create Page</button>
</form>
</x-admin.builder-layout>
@endsection
