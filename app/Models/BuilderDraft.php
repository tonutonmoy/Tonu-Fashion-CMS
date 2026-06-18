<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuilderDraft extends Model
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
