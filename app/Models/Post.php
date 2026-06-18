<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends BaseModel
{
    use HasTranslations;

    protected $fillable = [
        'blog_category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'og_image',
        'status',
        'published_at',
        'tag_ids',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'published_at' => 'datetime',
            'tag_ids' => 'array',
            'translations' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getTagsAttribute(): Collection
    {
        if ($this->relationLoaded('tags')) {
            return $this->getRelation('tags');
        }

        $ids = $this->tag_ids ?? [];

        if ($ids === []) {
            return new Collection;
        }

        return Tag::query()->whereIn('id', $ids)->get();
    }
}
