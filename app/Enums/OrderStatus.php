<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case ParcelCreated = 'parcel_created';
    case Picked = 'picked';
    case InTransit = 'in_transit';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::ParcelCreated => 'Parcel Created',
            self::Picked => 'Picked',
            self::InTransit => 'In Transit',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
            self::Returned => 'Returned',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Confirmed => 'blue',
            self::Processing => 'indigo',
            self::ParcelCreated => 'cyan',
            self::Picked => 'violet',
            self::InTransit => 'purple',
            self::Shipped => 'purple',
            self::Delivered => 'green',
            self::Cancelled => 'red',
            self::Returned => 'orange',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled, self::Returned], true);
    }

    public function canTransitionTo(self $status): bool
    {
        if ($this === $status || $this->isTerminal()) {
            return false;
        }

        return match ($this) {
            self::Pending => in_array($status, [self::Confirmed, self::Cancelled], true),
            self::Confirmed => in_array($status, [self::Processing, self::ParcelCreated, self::Cancelled], true),
            self::Processing => in_array($status, [self::ParcelCreated, self::Picked, self::InTransit, self::Shipped, self::Cancelled], true),
            self::ParcelCreated => in_array($status, [self::Picked, self::InTransit, self::Shipped, self::Cancelled], true),
            self::Picked => in_array($status, [self::InTransit, self::Shipped, self::Delivered, self::Cancelled], true),
            self::InTransit => in_array($status, [self::Shipped, self::Delivered, self::Returned, self::Cancelled], true),
            self::Shipped => in_array($status, [self::Delivered, self::Returned], true),
            default => false,
        };
    }
}
