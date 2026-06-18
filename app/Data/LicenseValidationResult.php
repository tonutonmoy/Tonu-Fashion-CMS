<?php

namespace App\Data;

use App\Enums\LicenseStatus;
use App\Models\License;

class LicenseValidationResult
{
    public function __construct(
        public bool $valid,
        public string $reason = 'valid',
        public ?License $license = null,
        public ?string $message = null,
    ) {}

    public static function valid(License $license): self
    {
        return new self(true, 'valid', $license);
    }

    public static function invalid(string $message, string $reason = 'invalid'): self
    {
        return new self(false, $reason, message: $message);
    }

    public static function expired(License $license, string $message): self
    {
        return new self(false, 'expired', $license, $message);
    }

    public static function suspended(License $license, string $message): self
    {
        return new self(false, 'suspended', $license, $message);
    }

    public function isExpired(): bool
    {
        return $this->reason === 'expired';
    }
}
