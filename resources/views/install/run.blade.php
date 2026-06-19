@extends('install.layout')
@section('title', 'Install')
@section('step', 5)
@section('content')
<h2 class="text-2xl font-bold mb-2">Ready to Install</h2>
<p class="text-gray-600 mb-6">We will configure MySQL, seed demo data, link storage, and warm the storefront cache.</p>

<div class="grid sm:grid-cols-2 gap-4 mb-6 text-sm">
    <div class="rounded-lg border p-4">
        <p class="font-semibold text-gray-800">Database</p>
        <p class="text-gray-500">MySQL</p>
    </div>
    <div class="rounded-lg border p-4">
        <p class="font-semibold text-gray-800">Media</p>
        <p class="text-gray-500">Local storage (WebP)</p>
    </div>
</div>

<ul class="list-disc pl-5 text-sm text-gray-600 space-y-1 mb-6">
    <li>Run MySQL migrations with foreign keys and indexes</li>
    <li>Seed categories, products, theme, CMS, and settings</li>
    <li>Create storage symlink for uploaded images</li>
    <li>Warm homepage, shop, and product caches</li>
</ul>

<form action="{{ route('install.run') }}" method="POST">
    @csrf
    <button type="submit" class="btn-primary w-full sm:w-auto">Install Now →</button>
</form>
@endsection
