@extends('layouts.admin')
@section('title', 'Expenses')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Expenses</h2>
        <p class="text-sm text-gray-500">Track marketing, courier, rent, and other operating costs.</p>
    </div>
    <button type="button" class="btn-primary" data-expense-modal-open>Add Expense</button>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                    <th class="px-4 py-3 text-left">Recorded By</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($expenses as $expense)
                <tr>
                    <td class="px-4 py-3">{{ $expense->expense_date?->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $expense->title }}</p>
                        @if($expense->note)
                        <p class="text-xs text-gray-500 truncate max-w-xs">{{ $expense->note }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge bg-{{ $expense->category->color() }}-100 text-{{ $expense->category->color() }}-800">{{ $expense->category->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">{{ format_bdt($expense->amount) }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $expense->admin?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <x-admin.action-group>
                            <x-admin.action-btn variant="edit" :href="route('admin.expenses.edit', $expense)" />
                            <x-admin.action-btn variant="delete" :action="route('admin.expenses.destroy', $expense)" method="DELETE" :confirm="true" confirm-message="Delete this expense?" />
                        </x-admin.action-group>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No expenses recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $expenses->links() }}</div>

<div id="expense-modal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" data-expense-modal-close></div>
    <div class="relative z-10 flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Add Expense</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" data-expense-modal-close aria-label="Close">&times;</button>
            </div>
            @include('admin.expenses._form', [
                'action' => route('admin.expenses.store'),
                'method' => 'POST',
                'expense' => null,
                'categories' => $categories,
            ])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modal = document.getElementById('expense-modal');
document.querySelector('[data-expense-modal-open]')?.addEventListener('click', () => {
    modal.classList.remove('hidden');
});
modal?.querySelectorAll('[data-expense-modal-close]').forEach((el) => {
    el.addEventListener('click', () => modal.classList.add('hidden'));
});
</script>
@endpush
