<?php

namespace App\Models;

use App\Enums\LicenseStatus;

class License extends BaseModel
{
    protected $fillable = [
        'license_key',
        'license_key_hash',
        'licensed_domain',
        'customer_name',
        'customer_email',
        'plan',
        'issued_at',
        'expires_at',
        'status',
        'last_check_at',
        'last_ip',
        'verification_signature',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => LicenseStatus::class,
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_check_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === LicenseStatus::Active && ! $this->isExpired();
    }
}
