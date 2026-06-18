<?php

namespace App\Enums;

enum LicenseStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Invalid = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Expired => 'Expired',
            self::Suspended => 'Suspended',
            self::Invalid => 'Invalid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Expired => 'orange',
            self::Suspended => 'red',
            self::Invalid => 'gray',
        };
    }
}
