@extends('layouts.admin')
@section('title', 'Edit Expense')
@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('admin.expenses.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to expenses</a>
        <h2 class="text-xl font-semibold mt-2">Edit Expense</h2>
    </div>
    <div class="card p-6">
        @include('admin.expenses._form', [
            'action' => route('admin.expenses.update', $expense),
            'method' => 'PUT',
            'expense' => $expense,
            'categories' => $categories,
            'cancelUrl' => route('admin.expenses.index'),
        ])
    </div>
</div>
@endsection
