<?php

namespace App\Enums;

enum ThemeSlug: string
{
    case FashionModern = 'fashion-modern';
    case FashionClassic = 'fashion-classic';
    case FashionLuxury = 'fashion-luxury';
    case FashionMinimal = 'fashion-minimal';

    public function label(): string
    {
        return match ($this) {
            self::FashionModern => 'Fashion Modern',
            self::FashionClassic => 'Fashion Classic',
            self::FashionLuxury => 'Fashion Luxury',
            self::FashionMinimal => 'Fashion Minimal',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FashionModern => 'Bold gradients, contemporary cards, ideal for trendy FB shops',
            self::FashionClassic => 'Elegant serif typography with timeless layout',
            self::FashionLuxury => 'Dark premium aesthetic with gold accents',
            self::FashionMinimal => 'Clean whitespace-focused minimal design',
        };
    }
}
