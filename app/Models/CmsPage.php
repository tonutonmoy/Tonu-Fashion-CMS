<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use App\Enums\ContentStatus;

class CmsPage extends BaseModel
{
    use HasTranslations;

    protected $table = 'cms_pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'banner_image',
        'meta_title',
        'meta_description',
        'og_image',
        'status',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'translations' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
