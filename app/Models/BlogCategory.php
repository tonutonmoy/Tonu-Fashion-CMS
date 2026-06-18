<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends BaseModel
{
    protected $fillable = ['name', 'slug', 'status'];

    protected function casts(): array
    {
        return [
            'status' => RecordStatus::class,
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
