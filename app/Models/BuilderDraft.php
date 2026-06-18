<?php

namespace App\Models;


class BuilderDraft extends BaseModel
{
    protected $fillable = [
        'has_changes',
        'theme',
        'homepage',
        'hero_slides',
        'footer',
    ];

    protected function casts(): array
    {
        return [
            'has_changes' => 'boolean',
            'theme' => 'array',
            'homepage' => 'array',
            'hero_slides' => 'array',
            'footer' => 'array',
        ];
    }
}
