<?php

namespace App\Repositories\Eloquent;

use App\Models\Expense;
use App\Models\ExpenseEntry;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function paginateAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->with('admin:id,name')
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function find(string|int $id): ?object
    {
        return $this->query()->find($id);
    }

    public function create(array $data): object
    {
        return $this->query()->create($data);
    }

    public function update(string|int $id, array $data): object
    {
        $record = $this->query()->findOrFail($id);
        $record->update($data);

        return $record->fresh();
    }

    public function delete(string|int $id): bool
    {
        return (bool) $this->query()->whereKey($id)->delete();
    }

    public function sumBetween(Carbon $start, Carbon $end): float
    {
        return (float) $this->query()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');
    }

    public function chartTotalsBetween(Carbon $start, Carbon $end): Collection
    {
        if ($this->usesMongo()) {
            return collect(
                Expense::query()
                    ->raw(function ($collection) use ($start, $end) {
                        return $collection->aggregate([
                            [
                                '$match' => [
                                    'expense_date' => [
                                        '$gte' => $start->copy()->startOfDay()->toDateTime(),
                                        '$lte' => $end->copy()->endOfDay()->toDateTime(),
                                    ],
                                ],
                            ],
                            [
                                '$group' => [
                                    '_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$expense_date']],
                                    'total' => ['$sum' => '$amount'],
                                ],
                            ],
                            ['$sort' => ['_id' => 1]],
                        ]);
                    })
            )->map(fn ($row) => [
                'period' => $row->_id,
                'total' => (float) ($row->total ?? 0),
            ]);
        }

        return DB::table('expenses')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as period, SUM(amount) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn ($row) => [
                'period' => $row->period,
                'total' => (float) $row->total,
            ]);
    }

    private function query()
    {
        return $this->usesMongo()
            ? Expense::query()
            : ExpenseEntry::query();
    }

    private function usesMongo(): bool
    {
        return config('database.default') === 'mongodb';
    }
}
