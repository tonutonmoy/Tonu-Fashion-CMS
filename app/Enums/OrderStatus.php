<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case CallingStage = 'calling_stage';
    case Courier = 'courier';
    case Delivered = 'delivered';
    case Payment = 'payment';
    case Cancelled = 'cancelled';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::CallingStage => 'Calling Stage',
            self::Courier => 'Courier',
            self::Delivered => 'Delivered',
            self::Payment => 'Payment',
            self::Cancelled => 'Cancel',
            self::Returned => 'Return',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::CallingStage => 'blue',
            self::Courier => 'cyan',
            self::Delivered => 'green',
            self::Payment => 'indigo',
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
        if ($this === $status) {
            return false;
        }

        if ($this->isTerminal()) {
            return false;
        }

        return true;
    }

    /** @return array<string, self> */
    public static function legacyMap(): array
    {
        return [
            'confirmed' => self::CallingStage,
            'processing' => self::CallingStage,
            'parcel_created' => self::Courier,
            'picked' => self::Courier,
            'in_transit' => self::Courier,
            'shipped' => self::Courier,
        ];
    }

    public static function tryFromLegacy(string $value): ?self
    {
        $resolved = self::tryFrom($value);

        if ($resolved !== null) {
            return $resolved;
        }

        return self::legacyMap()[$value] ?? null;
    }
}
