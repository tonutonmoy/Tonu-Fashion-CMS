<?php

namespace App\Models;

use App\Enums\AdminPermission;
use App\Enums\RecordStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'order_blocked',
        'blog_blocked',
        'admin_permissions',
        'avatar',
        'low_stock_alerts_seen_hash',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => RecordStatus::class,
            'order_blocked' => 'boolean',
            'blog_blocked' => 'boolean',
            'admin_permissions' => 'array',
        ];
    }

    /** @return array<string, bool> */
    public function adminPermissionsMap(): array
    {
        if ($this->role === UserRole::SuperAdmin) {
            return collect(AdminPermission::cases())
                ->mapWithKeys(fn (AdminPermission $p) => [$p->value => true])
                ->all();
        }

        if (! $this->isAdmin()) {
            return AdminPermission::defaultMapForRole(UserRole::Staff);
        }

        if (is_array($this->admin_permissions) && $this->admin_permissions !== []) {
            return array_merge(
                AdminPermission::defaultMapForRole($this->role),
                collect($this->admin_permissions)->map(fn ($v) => (bool) $v)->all()
            );
        }

        return AdminPermission::defaultMapForRole($this->role);
    }

    public function canAdmin(AdminPermission|string $permission): bool
    {
        if ($this->role === UserRole::SuperAdmin) {
            return true;
        }

        if (! $this->isAdmin() || ! $this->isAccountActive()) {
            return false;
        }

        $key = $permission instanceof AdminPermission ? $permission->value : $permission;

        return (bool) ($this->adminPermissionsMap()[$key] ?? false);
    }

    /** @return list<string> */
    public function enabledAdminPermissionLabels(): array
    {
        return collect($this->adminPermissionsMap())
            ->filter()
            ->keys()
            ->map(fn (string $key) => AdminPermission::from($key)->label())
            ->values()
            ->all();
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isAccountActive(): bool
    {
        return $this->status === RecordStatus::Active;
    }

    public function canPlaceOrders(): bool
    {
        return $this->isAccountActive() && ! $this->order_blocked;
    }

    public function canAccessBlog(): bool
    {
        return $this->isAccountActive() && ! $this->blog_blocked;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
