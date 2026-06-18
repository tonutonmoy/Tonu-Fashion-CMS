<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_link',
        'desktop_image',
        'mobile_image',
        'video_url',
        'overlay_color',
        'content_layout',
        'title_size',
        'subtitle_size',
        'button_size',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'title_size' => 'float',
            'subtitle_size' => 'float',
            'button_size' => 'float',
            'status' => RecordStatus::class,
        ];
    }
}
