<?php

namespace App\Models;


class HomepageSection extends BaseModel
{
    protected $fillable = [
        'section_key',
        'title',
        'enabled',
        'sort_order',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'sort_order' => 'integer',
            'settings' => 'array',
        ];
    }
}
