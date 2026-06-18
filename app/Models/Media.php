<?php

namespace App\Models;


class Media extends BaseModel
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
