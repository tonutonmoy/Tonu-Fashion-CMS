@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
@if($errors->has('_token'))
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first('_token') }}</div>
@endif
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
    @csrf @method('PUT')
    @include('admin.products._form', ['product' => $product])
    <button type="submit" class="btn-primary">Update Product</button>
</form>
@endsection
