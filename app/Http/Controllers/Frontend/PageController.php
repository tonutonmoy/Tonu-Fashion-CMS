<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CmsPageRepositoryInterface;
use App\Services\SeoService;
use Illuminate\View\View;

class PageController extends Controller
{
    use RendersThemeViews;

    public function __construct(
        private CmsPageRepositoryInterface $pages,
        private SeoService $seo
    ) {}

    public function show(string $slug): View
    {
        $page = $this->pages->findBySlug($slug);

        if (! $page) {
            abort(404);
        }

        $canPreview = request()->boolean('preview')
            && auth()->check()
            && auth()->user()->role->canManageSettings();

        if ($page->status !== ContentStatus::Published && ! $canPreview) {
            abort(404);
        }

        return $this->themeView('page', [
            'page' => $page,
            'seo' => $this->seo->meta([
                'title' => $page->meta_title ?: $page->title,
                'description' => $page->meta_description,
                'image' => image_url($page->og_image ?: $page->banner_image),
            ]),
        ]);
    }
}
