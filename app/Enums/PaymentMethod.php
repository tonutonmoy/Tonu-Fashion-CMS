<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CashOnDelivery = 'cash_on_delivery';
    case Bkash = 'bkash';
    case Nagad = 'nagad';
    case SslCommerz = 'sslcommerz';

    public function label(): string
    {
        return match ($this) {
            self::CashOnDelivery => 'Cash on Delivery',
            self::Bkash => 'bKash',
            self::Nagad => 'Nagad',
            self::SslCommerz => 'Card / Mobile Banking (SSLCommerz)',
        };
    }

    public function isOnline(): bool
    {
        return $this !== self::CashOnDelivery;
    }
}
