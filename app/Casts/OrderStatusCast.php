<?php

namespace App\Casts;

use App\Enums\OrderStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class OrderStatusCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?OrderStatus
    {
        if ($value === null || $value === '') {
            return null;
        }

        return OrderStatus::tryFromLegacy((string) $value) ?? OrderStatus::Pending;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof OrderStatus) {
            return $value->value;
        }

        $resolved = OrderStatus::tryFromLegacy((string) $value);

        return $resolved?->value ?? (string) $value;
    }
}
