<?php

namespace App\Data\Payments;

class PaymentInitResult
{
    public function __construct(
        public bool $success,
        public ?string $redirectUrl = null,
        public ?string $gatewayPaymentId = null,
        public ?string $message = null,
        public array $raw = [],
    ) {}

    public static function redirect(string $url, ?string $gatewayPaymentId = null, array $raw = []): self
    {
        return new self(true, $url, $gatewayPaymentId, raw: $raw);
    }

    public static function failure(string $message, array $raw = []): self
    {
        return new self(false, message: $message, raw: $raw);
    }
}
