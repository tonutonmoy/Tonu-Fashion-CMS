<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'SUPER_ADMIN';
    case Admin = 'ADMIN';
    case Staff = 'STAFF';
    case Customer = 'CUSTOMER';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Staff => 'Staff',
            self::Customer => 'Customer',
        };
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin, self::Staff], true);
    }

    public function canManageSettings(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin], true);
    }

    public function canManageUsers(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function canManageStore(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin, self::Staff], true);
    }

    public function canManageOrders(): bool
    {
        return $this->canManageStore();
    }

    public function canManageCustomers(): bool
    {
        return $this->canManageStore();
    }

    /** Roles that Super Admin can assign when creating team members. */
    public static function assignableTeamRoles(): array
    {
        return [self::Admin, self::Staff];
    }
}
