<?php

namespace App\Enums;

enum StockMovementType: string
{
    case AdjustIn = 'adjust_in';
    case AdjustOut = 'adjust_out';
    case Reserve = 'reserve';
    case Deduct = 'deduct';
    case Rollback = 'rollback';

    public function label(): string
    {
        return match ($this) {
            self::AdjustIn => 'Adjust In',
            self::AdjustOut => 'Adjust Out',
            self::Reserve => 'Reserve',
            self::Deduct => 'Deduct',
            self::Rollback => 'Rollback',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AdjustIn => 'green',
            self::AdjustOut => 'orange',
            self::Reserve => 'blue',
            self::Deduct => 'red',
            self::Rollback => 'yellow',
        };
    }
}
