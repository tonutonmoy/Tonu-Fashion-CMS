@extends('layouts.admin')
@section('title', 'Add Category')
@section('content')
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="max-w-xl card p-6 space-y-4">
    @csrf
    <div>
        <label class="label">Name</label>
        <input name="name" class="input" required data-slug-source data-slug-target="#category-slug" value="{{ old('name') }}">
    </div>
    <div>
        <label class="label">Slug</label>
        <input name="slug" id="category-slug" class="input" value="{{ old('slug') }}" placeholder="auto-generated-from-name">
    </div>
    <x-admin.image-uploader name="image" label="Category Image" hint="Square image works best" />
    <div><label class="label">Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="input"></div>
    <div><label class="label">Status</label><select name="status" class="input"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    <button class="btn-primary">Save</button>
</form>
@endsection
