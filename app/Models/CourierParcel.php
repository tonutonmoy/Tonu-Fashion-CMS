<?php

namespace App\Models;

use App\Enums\CourierType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourierParcel extends BaseModel
{
    protected $fillable = [
        'order_id',
        'courier_name',
        'consignment_id',
        'tracking_code',
        'tracking_url',
        'current_status',
        'last_synced_at',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CourierTrackingHistory::class)->orderByDesc('recorded_at');
    }

    public function courierType(): ?CourierType
    {
        return CourierType::tryFrom($this->courier_name);
    }

    public function isActive(): bool
    {
        return ! in_array(strtolower($this->current_status), ['delivered', 'returned', 'cancelled'], true);
    }
}
