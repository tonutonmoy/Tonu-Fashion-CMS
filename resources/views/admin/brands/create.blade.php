@extends('layouts.admin')
@section('title', 'Add Brand')
@section('content')
<form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="max-w-xl card p-6 space-y-4">
    @csrf
    <div>
        <label class="label">Name</label>
        <input name="name" class="input" required data-slug-source data-slug-target="#brand-slug" value="{{ old('name') }}">
    </div>
    <div>
        <label class="label">Slug</label>
        <input name="slug" id="brand-slug" class="input" value="{{ old('slug') }}" placeholder="auto-generated-from-name">
    </div>
    <x-admin.image-uploader name="logo" label="Brand Logo" hint="Transparent PNG recommended" />
    <div><label class="label">Status</label><select name="status" class="input"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    <button class="btn-primary">Save</button>
</form>
@endsection
