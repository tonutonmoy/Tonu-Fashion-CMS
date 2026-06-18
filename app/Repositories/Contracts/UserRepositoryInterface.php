<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function paginateCustomers(array $filters = [], ?int $perPage = null): LengthAwarePaginator;

    public function paginateAdmins(?int $perPage = null): LengthAwarePaginator;

    public function paginateTeamMembers(array $filters = [], ?int $perPage = null): LengthAwarePaginator;
}
