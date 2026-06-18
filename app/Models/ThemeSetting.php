<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = [
        'active_theme',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'header_style',
        'footer_style',
        'button_radius',
        'container_width',
        'logo',
        'favicon',
        'meta_title',
        'meta_description',
        'og_image',
        'json_ld_schema',
        'asset_version',
    ];

    protected function casts(): array
    {
        return [
            'json_ld_schema' => 'array',
        ];
    }
}
