<?php

return [
    'default' => 'fashion-modern',

    'global_defaults' => [
        'active_theme' => 'fashion-modern',
        'primary_color' => '#dc2626',
        'secondary_color' => '#1e293b',
        'accent_color' => '#f97316',
        'font_family' => 'Inter',
        'header_style' => 'default',
        'footer_style' => 'default',
        'button_radius' => '0.5rem',
        'container_width' => '80rem',
    ],

    'themes' => [
        'fashion-modern' => [
            'name' => 'Fashion Modern',
            'description' => 'Bold, contemporary design for trendy fashion stores',
            'preview' => '/themes/fashion-modern/preview.jpg',
            'fonts' => ['Inter', 'Poppins', 'Montserrat'],
            'defaults' => [
                'primary_color' => '#dc2626',
                'secondary_color' => '#1e293b',
                'accent_color' => '#f97316',
                'font_family' => 'Inter',
                'header_style' => 'default',
                'footer_style' => 'default',
                'button_radius' => '0.5rem',
                'container_width' => '80rem',
            ],
        ],
        'fashion-classic' => [
            'name' => 'Fashion Classic',
            'description' => 'Timeless editorial elegance with serif typography',
            'preview' => '/themes/fashion-classic/preview.jpg',
            'fonts' => ['Playfair Display', 'Lora', 'Merriweather'],
            'defaults' => [
                'primary_color' => '#1e3a5f',
                'secondary_color' => '#0f172a',
                'accent_color' => '#c9a227',
                'font_family' => 'Playfair Display',
                'header_style' => 'centered',
                'footer_style' => 'minimal',
                'button_radius' => '0',
                'container_width' => '72rem',
            ],
        ],
        'fashion-luxury' => [
            'name' => 'Fashion Luxury',
            'description' => 'Premium dark aesthetic with gold accents',
            'preview' => '/themes/fashion-luxury/preview.jpg',
            'fonts' => ['Cormorant Garamond', 'Cinzel', 'Inter'],
            'defaults' => [
                'primary_color' => '#d4af37',
                'secondary_color' => '#0a0a0a',
                'accent_color' => '#f5e6c8',
                'font_family' => 'Cormorant Garamond',
                'header_style' => 'sticky',
                'footer_style' => 'default',
                'button_radius' => '0.25rem',
                'container_width' => '80rem',
            ],
        ],
        'fashion-minimal' => [
            'name' => 'Fashion Minimal',
            'description' => 'Clean whitespace-focused minimal design',
            'preview' => '/themes/fashion-minimal/preview.jpg',
            'fonts' => ['DM Sans', 'Inter', 'Outfit'],
            'defaults' => [
                'primary_color' => '#111827',
                'secondary_color' => '#374151',
                'accent_color' => '#6b7280',
                'font_family' => 'DM Sans',
                'header_style' => 'default',
                'footer_style' => 'minimal',
                'button_radius' => '9999px',
                'container_width' => '64rem',
            ],
        ],
    ],

    'header_styles' => [
        'default' => 'Default',
        'centered' => 'Centered Logo',
        'transparent' => 'Transparent Hero',
        'sticky' => 'Sticky Compact',
    ],

    'footer_styles' => [
        'default' => 'Default 4-Column',
        'minimal' => 'Minimal Centered',
        'expanded' => 'Expanded with Newsletter',
    ],

    'hero_content_layouts' => [
        'centered' => 'Centered',
        'left' => 'Left Aligned',
        'right' => 'Right Aligned',
        'bottom' => 'Bottom Bar',
    ],

    'hero_typography_defaults' => [
        'title_size' => 48,
        'subtitle_size' => 20,
        'button_size' => 14,
    ],

    'hero_size_defaults' => [
        'button_width' => 140,
        'button_height' => 44,
    ],

    'hero_content_defaults' => [
        'content_layout' => 'centered',
        'show_title' => true,
        'show_subtitle' => true,
        'show_button' => true,
        'title' => 'New Collection',
        'subtitle' => 'Discover the latest styles',
        'button_text' => 'Shop Now',
        'button_link' => '/shop',
    ],

    'google_fonts' => [
        'Inter' => 'Inter',
        'Poppins' => 'Poppins',
        'Montserrat' => 'Montserrat',
        'Playfair Display' => 'Playfair+Display',
        'Lora' => 'Lora',
        'Merriweather' => 'Merriweather',
        'Cormorant Garamond' => 'Cormorant+Garamond',
        'Cinzel' => 'Cinzel',
        'DM Sans' => 'DM+Sans',
        'Outfit' => 'Outfit',
    ],
];
