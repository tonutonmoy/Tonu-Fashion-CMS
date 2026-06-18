<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'folder',
        'filename',
        'path',
        'mime_type',
        'size',
        'alt',
        'width',
        'height',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function getUrlAttribute(): string
    {
        return image_url($this->path) ?? '';
    }
}
