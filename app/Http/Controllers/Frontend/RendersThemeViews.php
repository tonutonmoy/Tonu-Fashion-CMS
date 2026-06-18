<?php

namespace App\Http\Controllers\Frontend;

use App\Services\ThemeService;
use Illuminate\View\View;

trait RendersThemeViews
{
    protected function themeView(string $template, array $data = []): View
    {
        return app(ThemeService::class)->view($template, $data);
    }
}
