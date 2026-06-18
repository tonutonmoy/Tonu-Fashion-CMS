<?php

namespace App\Data\Payments;

class PaymentVerifyResult
{
    public function __construct(
        public bool $success,
        public bool $paid,
        public ?string $gatewayPaymentId = null,
        public ?string $message = null,
        public array $raw = [],
    ) {}

    public static function paid(?string $gatewayPaymentId = null, array $raw = []): self
    {
        return new self(true, true, $gatewayPaymentId, raw: $raw);
    }

    public static function unpaid(string $message, array $raw = []): self
    {
        return new self(true, false, message: $message, raw: $raw);
    }

    public static function failure(string $message, array $raw = []): self
    {
        return new self(false, false, message: $message, raw: $raw);
    }
}
