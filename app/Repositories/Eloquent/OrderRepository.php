<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->newQuery()
            ->with(['items.product', 'items.variant', 'user', 'coupon', 'courierParcel.histories'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function paginateAdmin(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('fashion.pagination.orders', admin_per_page());

        $query = $this->model->newQuery()->with(['user', 'items', 'courierParcel']);

        $scope = $filters['scope'] ?? 'today';
        if ($scope === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $digits = preg_replace('/[^0-9]/', '', $search) ?? '';

            $query->where(function ($q) use ($search, $digits) {
                $q->where('order_number', 'like', "%{$search}%");

                if ($digits !== '') {
                    $q->orWhere('customer_phone', 'like', "%{$digits}%");
                    if (strlen($digits) >= 10) {
                        $q->orWhere('customer_phone', 'like', '%'.substr($digits, -10));
                    }
                } else {
                    $q->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                }
            });
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('items')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
