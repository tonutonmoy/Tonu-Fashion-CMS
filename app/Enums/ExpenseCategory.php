<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Marketing = 'marketing';
    case Courier = 'courier';
    case Rent = 'rent';
    case Utility = 'utility';
    case Salary = 'salary';
    case Others = 'others';

    public function label(): string
    {
        return match ($this) {
            self::Marketing => 'Marketing',
            self::Courier => 'Courier',
            self::Rent => 'Rent',
            self::Utility => 'Utility',
            self::Salary => 'Salary',
            self::Others => 'Others',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Marketing => 'purple',
            self::Courier => 'cyan',
            self::Rent => 'orange',
            self::Utility => 'blue',
            self::Salary => 'indigo',
            self::Others => 'gray',
        };
    }
}
