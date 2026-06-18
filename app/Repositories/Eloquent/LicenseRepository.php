<?php

namespace App\Repositories\Eloquent;

use App\Enums\LicenseStatus;
use App\Models\License;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LicenseRepository extends BaseRepository implements LicenseRepositoryInterface
{
    public function __construct(License $model)
    {
        parent::__construct($model);
    }

    public function findByKey(string $licenseKey): ?License
    {
        return $this->model->newQuery()->where('license_key', $licenseKey)->first();
    }

    public function findByKeyHash(string $hash): ?License
    {
        return $this->model->newQuery()->where('license_key_hash', $hash)->first();
    }

    public function findByDomain(string $domain): ?License
    {
        return $this->model->newQuery()->where('licensed_domain', $domain)->first();
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= admin_per_page();

        $query = $this->model->newQuery()->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('license_key', 'like', "%{$search}%")
                    ->orWhere('licensed_domain', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function stats(): array
    {
        return [
            'total' => $this->model->newQuery()->count(),
            'active' => $this->model->newQuery()->where('status', LicenseStatus::Active)->count(),
            'expired' => $this->model->newQuery()->where('status', LicenseStatus::Expired)->count(),
            'suspended' => $this->model->newQuery()->where('status', LicenseStatus::Suspended)->count(),
        ];
    }
}
