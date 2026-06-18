<?php

namespace App\Repositories\Contracts;

use App\Models\License;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LicenseRepositoryInterface extends BaseRepositoryInterface
{
    public function findByKey(string $licenseKey): ?License;

    public function findByKeyHash(string $hash): ?License;

    public function findByDomain(string $domain): ?License;

    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function stats(): array;
}
