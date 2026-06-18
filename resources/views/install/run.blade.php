@extends('install.layout')
@section('title', 'Install')
@section('step', 5)
@section('content')
<h2 class="text-2xl font-bold mb-2">Ready to Install</h2>
<p class="text-gray-600 mb-6">Review your settings and run the installation. This may take a minute on shared hosting.</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 text-sm">
    <div class="rounded-xl bg-gray-50 border p-4">
        <h3 class="font-semibold mb-2">Database</h3>
        <p>{{ $summary['database']['db_database'] ?? '' }}</p>
        <p class="text-gray-500">{{ $summary['database']['db_host'] ?? '' }}</p>
    </div>
    <div class="rounded-xl bg-gray-50 border p-4">
        <h3 class="font-semibold mb-2">Store</h3>
        <p>{{ $summary['store']['store_name'] ?? '' }}</p>
        <p class="text-gray-500">{{ $summary['store']['store_email'] ?? '' }}</p>
    </div>
    <div class="rounded-xl bg-gray-50 border p-4">
        <h3 class="font-semibold mb-2">Admin</h3>
        <p>{{ $summary['admin']['name'] ?? '' }}</p>
        <p class="text-gray-500">{{ $summary['admin']['email'] ?? '' }}</p>
    </div>
</div>

<ul class="text-sm text-gray-600 space-y-2 mb-8 list-disc list-inside">
    <li>Generate application key</li>
    <li>Run database migrations</li>
    <li>Seed default modules (theme, marketing, courier, payment)</li>
    <li>Create storage link</li>
    <li>Create super admin user</li>
    <li>Cache config, routes, and views</li>
</ul>

<form action="{{ route('install.execute') }}" method="POST" class="flex justify-between">
    @csrf
    <a href="{{ route('install.admin') }}" class="btn-secondary">← Previous</a>
    <button type="submit" class="btn-primary">Install Fashion BD</button>
</form>
@endsection
