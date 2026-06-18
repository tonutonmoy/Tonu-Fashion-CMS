<?php

namespace App\Data\Courier;

class CourierParcelResult
{
    public function __construct(
        public bool $success,
        public ?string $consignmentId = null,
        public ?string $trackingCode = null,
        public ?string $trackingUrl = null,
        public ?string $status = null,
        public ?string $message = null,
        public array $raw = [],
    ) {}

    public static function success(
        string $consignmentId,
        string $trackingCode,
        string $trackingUrl,
        string $status = 'created',
        array $raw = []
    ): self {
        return new self(true, $consignmentId, $trackingCode, $trackingUrl, $status, null, $raw);
    }

    public static function failure(string $message, array $raw = []): self
    {
        return new self(false, message: $message, raw: $raw);
    }
}
