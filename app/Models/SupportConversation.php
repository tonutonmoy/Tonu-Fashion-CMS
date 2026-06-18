<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportConversation extends Model
{
    protected $fillable = [
        'uuid',
        'guest_token',
        'user_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'status',
        'admin_unread_count',
        'customer_unread_count',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'admin_unread_count' => 'integer',
            'customer_unread_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $conversation) {
            if (empty($conversation->uuid)) {
                $conversation->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('id');
    }

    public function latestMessage(): HasMany
    {
        return $this->messages()->latest('id')->limit(1);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
