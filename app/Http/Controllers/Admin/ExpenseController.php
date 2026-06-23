<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseRequest;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
    ) {}

    public function index(): View
    {
        return view('admin.expenses.index', [
            'expenses' => $this->expenses->paginateAdmin(),
            'categories' => ExpenseCategory::cases(),
        ]);
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $this->expenses->create([
            ...$request->validated(),
            'admin_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Expense recorded.');
    }

    public function edit(string $expense): View
    {
        $record = $this->expenses->find($expense);
        abort_if($record === null, 404);

        return view('admin.expenses.form', [
            'expense' => $record,
            'categories' => ExpenseCategory::cases(),
        ]);
    }

    public function update(ExpenseRequest $request, string $expense): RedirectResponse
    {
        $this->expenses->update($expense, $request->validated());

        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(string $expense): RedirectResponse
    {
        $this->expenses->delete($expense);

        return back()->with('success', 'Expense deleted.');
    }
}
