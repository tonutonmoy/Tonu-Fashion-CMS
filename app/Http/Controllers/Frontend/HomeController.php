<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\HomepageBuilderService;
use App\Services\SeoService;
use Illuminate\View\View;

class HomeController extends Controller
{
    use RendersThemeViews;

    public function __construct(
        private HomepageBuilderService $homepage,
        private SeoService $seo
    ) {}

    public function index(): View
    {
        $sections = $this->homepage->getPageData();

        return $this->themeView('home', [
            'sections' => $sections,
            'sectionKeys' => $this->homepage->getEnabledSectionKeys(),
            'hasHomeHero' => ! empty($sections['hero_slider']['config']['media'] ?? []),
            'seo' => $this->seo->themeMeta(),
        ]);
    }
}
