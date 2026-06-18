<?php

namespace App\Data\Courier;

class CourierStatusResult
{
    public function __construct(
        public bool $success,
        public string $status,
        public ?string $description = null,
        public array $history = [],
        public array $raw = [],
        public ?string $message = null,
    ) {}

    public static function success(string $status, ?string $description = null, array $history = [], array $raw = []): self
    {
        return new self(true, $status, $description, $history, $raw);
    }

    public static function failure(string $message, array $raw = []): self
    {
        return new self(false, 'unknown', message: $message, raw: $raw);
    }
}
