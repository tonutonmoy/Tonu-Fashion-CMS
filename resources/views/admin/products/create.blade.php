@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
    @csrf
    @include('admin.products._form')
    <button type="submit" class="btn-primary">Create Product</button>
</form>
@endsection
