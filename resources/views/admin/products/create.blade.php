@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
@if($errors->has('_token'))
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first('_token') }}</div>
@endif
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
    @csrf
    @include('admin.products._form')
    <button type="submit" class="btn-primary">Create Product</button>
</form>
@endsection
