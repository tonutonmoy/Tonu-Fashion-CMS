<?php

namespace App\Enums;

enum CourierType: string
{
    case Steadfast = 'steadfast';
    case Pathao = 'pathao';
    case RedX = 'redx';

    public function label(): string
    {
        return match ($this) {
            self::Steadfast => 'Steadfast',
            self::Pathao => 'Pathao Courier',
            self::RedX => 'RedX',
        };
    }

    public function configKey(): string
    {
        return $this->value;
    }
}
