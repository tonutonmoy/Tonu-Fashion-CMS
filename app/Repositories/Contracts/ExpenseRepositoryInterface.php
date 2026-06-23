<?php

namespace App\Repositories\Contracts;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ExpenseRepositoryInterface
{
    public function paginateAdmin(int $perPage = 15): LengthAwarePaginator;

    public function find(string|int $id): ?object;

    public function create(array $data): object;

    public function update(string|int $id, array $data): object;

    public function delete(string|int $id): bool;

    public function sumBetween(Carbon $start, Carbon $end): float;

    public function sumByCategoryBetween(string $category, Carbon $start, Carbon $end): float;

    public function chartTotalsBetween(Carbon $start, Carbon $end): Collection;
}
