<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierTrackingHistory extends BaseModel
{
    protected $fillable = [
        'courier_parcel_id',
        'status',
        'description',
        'recorded_at',
        'raw',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'raw' => 'array',
        ];
    }

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(CourierParcel::class, 'courier_parcel_id');
    }
}
