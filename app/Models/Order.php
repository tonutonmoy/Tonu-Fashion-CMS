<?php

namespace App\Models;

use App\Casts\OrderStatusCast;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
        'cogs',
        'coupon_id',
        'coupon_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_division',
        'shipping_district',
        'shipping_upazila',
        'shipping_area',
        'shipping_address',
        'order_note',
        'purchase_event_id',
        'fbp',
        'fbc',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatusCast::class,
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'cogs' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function courierParcel(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CourierParcel::class)->orderByDesc('id');
    }

    public function courierParcels(): HasMany
    {
        return $this->hasMany(CourierParcel::class);
    }

    public function hasParcel(): bool
    {
        return CourierParcel::query()->where('order_id', $this->id)->exists();
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }
}
