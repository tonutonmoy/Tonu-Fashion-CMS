@extends('layouts.admin')
@section('title', 'Edit Brand')
@section('content')
<form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data" class="max-w-xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="label">Name</label>
        <input name="name" value="{{ old('name', $brand->name) }}" class="input" required data-slug-source data-slug-target="#brand-slug">
    </div>
    <div>
        <label class="label">Slug</label>
        <input name="slug" id="brand-slug" value="{{ old('slug', $brand->slug) }}" class="input" data-manual="true">
    </div>
    <x-admin.image-uploader name="logo" label="Brand Logo" :existing-url="image_url($brand->logo)" hint="Transparent PNG recommended" />
    <div><label class="label">Status</label><select name="status" class="input"><option value="active" @selected($brand->status->value==='active')>Active</option><option value="inactive" @selected($brand->status->value==='inactive')>Inactive</option></select></div>
    <button class="btn-primary">Update</button>
</form>
@endsection
