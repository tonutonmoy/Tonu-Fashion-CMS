<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
        'og_image',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'status' => RecordStatus::class,
            'sort_order' => 'integer',
            'translations' => 'array',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
