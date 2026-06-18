@extends('layouts.admin')
@section('title', 'Edit Category')
@section('content')
<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="max-w-xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="label">Name</label>
        <input name="name" value="{{ old('name', $category->name) }}" class="input" required data-slug-source data-slug-target="#category-slug">
    </div>
    <div>
        <label class="label">Slug</label>
        <input name="slug" id="category-slug" value="{{ old('slug', $category->slug) }}" class="input" data-manual="true">
    </div>
    <x-admin.image-uploader name="image" label="Category Image" :existing-url="image_url($category->image)" hint="Square image works best" />
    <div><label class="label">Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="input"></div>
    <div><label class="label">Status</label><select name="status" class="input"><option value="active" @selected($category->status->value==='active')>Active</option><option value="inactive" @selected($category->status->value==='inactive')>Inactive</option></select></div>
    <button class="btn-primary">Update</button>
</form>
@endsection
