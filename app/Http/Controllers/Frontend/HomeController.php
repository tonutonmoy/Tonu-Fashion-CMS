<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\HomepageBuilderService;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
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
        $sections = $this->homepage->getInitialPageData();

        return $this->themeView('home', [
            'sections' => $sections,
            'sectionKeys' => $this->homepage->getEnabledSectionKeys(),
            'lazySectionKeys' => $this->homepage->getLazySectionKeys(),
            'hasHomeHero' => ! empty($sections['hero_slider']['config']['media'] ?? []),
            'seo' => $this->seo->themeMeta(),
        ]);
    }

    public function section(string $key): JsonResponse
    {
        if (! in_array($key, $this->homepage->getLazySectionKeys(), true)) {
            abort(404);
        }

        return response()->json([
            'html' => $this->homepage->renderLazySection($key),
        ]);
    }
}
