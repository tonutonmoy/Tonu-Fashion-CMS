@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
    @csrf @method('PUT')
    @include('admin.products._form', ['product' => $product])
    <button type="submit" class="btn-primary">Update Product</button>
</form>
@endsection
