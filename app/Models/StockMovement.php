<?php

namespace App\Models;

use App\Enums\StockMovementType;
use MongoDB\Laravel\Eloquent\Model as MongoModel;

class StockMovement extends MongoModel
{
    protected $connection = 'mongodb';

    protected $collection = 'stock_movements';

    protected $fillable = [
        'product_variant_id',
        'product_id',
        'order_id',
        'type',
        'quantity',
        'note',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
