<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public static function forVariant(string $variantId, int $requested, int $available): self
    {
        return new self("Insufficient stock for variant {$variantId}. Requested {$requested}, available {$available}.");
    }
}
