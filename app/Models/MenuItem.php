<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends BaseModel
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'page_id',
        'sort_order',
        'open_in_new_tab',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'open_in_new_tab' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function resolvedUrl(): string
    {
        if ($this->page_id && $this->page) {
            return route('pages.show', $this->page->slug);
        }

        return $this->url ?: '#';
    }
}
