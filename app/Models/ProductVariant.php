<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends BaseModel
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'image',
        'sku',
        'stock',
        'reserved_stock',
        'price_adjustment',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'price_adjustment' => 'float',
            'status' => RecordStatus::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([$this->size, $this->color]);

        return implode(' / ', $parts) ?: 'Default';
    }

    public function getPriceAttribute(): float
    {
        return $this->product->effective_price + (float) $this->price_adjustment;
    }

    public function availableStock(): int
    {
        return max(0, (int) $this->stock - (int) ($this->reserved_stock ?? 0));
    }
}
