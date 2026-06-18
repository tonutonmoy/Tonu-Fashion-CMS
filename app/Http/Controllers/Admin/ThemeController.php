<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterSettingsRequest;
use App\Http\Requests\Admin\HeroBuilderRequest;
use App\Http\Requests\Admin\HomepageSectionRequest;
use App\Http\Requests\Admin\ThemeCustomizerRequest;
use App\Http\Requests\Admin\ThemeSeoRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\HomepageSectionRepositoryInterface;
use App\Services\BuilderPublishService;
use App\Services\FooterBuilderService;
use App\Services\HeroBuilderService;
use App\Services\HomepageBuilderService;
use App\Services\ImageService;
use App\Services\ThemeCustomizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeController extends Controller
{
    public function __construct(
        private ThemeCustomizerService $customizer,
        private HomepageBuilderService $homepage,
        private FooterBuilderService $footer,
        private BuilderPublishService $publish,
        private HeroBuilderService $hero,
        private HomepageSectionRepositoryInterface $sections,
        private ImageService $images
    ) {}

    public function customizer(): View
    {
        return view('admin.theme.customizer', [
            'settings' => $this->customizer->get(),
            'themes' => $this->customizer->availableThemes(),
            'themeDefaults' => collect($this->customizer->availableThemes())
                ->mapWithKeys(fn ($theme, $slug) => [$slug => $theme['defaults'] ?? []])
                ->all(),
            'globalDefaults' => $this->customizer->globalDefaults(),
            'headerStyles' => config('themes.header_styles'),
            'footerStyles' => config('themes.footer_styles'),
            'fonts' => config('themes.google_fonts'),
        ]);
    }

    public function resetCustomizer(Request $request): RedirectResponse
    {
        $request->validate(['type' => 'required|in:all,colors,theme']);
        $this->customizer->reset($request->input('type'));

        return back()->with('success', match ($request->input('type')) {
            'colors' => 'Colors restored to theme defaults.',
            'theme' => 'Theme settings restored to defaults.',
            default => 'All theme settings restored to defaults.',
        });
    }

    public function updateCustomizer(ThemeCustomizerRequest $request): RedirectResponse
    {
        $this->customizer->update(
            $request->safe()->except(['logo', 'favicon', 'og_image']),
            $request->file('logo'),
            $request->file('favicon'),
            $request->file('og_image')
        );

        return back()->with('success', 'Theme draft saved. Click Publish to make it live on your store.');
    }

    public function seo(): View
    {
        return view('admin.theme.seo', ['settings' => $this->customizer->get()]);
    }

    public function updateSeo(ThemeSeoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('og_image')) {
            $current = $this->customizer->get();
            $this->images->delete($current->og_image);
            $data['og_image'] = $this->images->upload($request->file('og_image'), 'theme', 1200);
        }
        if (! empty($data['json_ld_schema'])) {
            $data['json_ld_schema'] = json_decode($data['json_ld_schema'], true) ?? null;
        } else {
            $data['json_ld_schema'] = null;
        }
        $this->customizer->update($data);

        return back()->with('success', 'SEO draft saved. Click Publish to make it live on your store.');
    }

    public function homepage(): View
    {
        return view('admin.theme.homepage', [
            'sections' => $this->publish->getEffectiveHomepageSections(),
            'categories' => Category::query()->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->limit(100)->get(),
        ]);
    }

    public function updateHomepageSection(HomepageSectionRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        $section = $this->publish->getEffectiveHomepageSections()->firstWhere('id', $id);

        if (! $section) {
            return back()->with('error', 'Section not found.');
        }

        if (isset($data['settings']) && is_array($data['settings'])) {
            $incoming = $data['settings'];

            if ($section->section_key === 'categories') {
                $incoming['category_ids'] = array_values(array_map(
                    'intval',
                    $request->input('settings.category_ids', [])
                ));
            }

            if ($section->section_key === 'featured_products') {
                $incoming['product_ids'] = array_values(array_map(
                    'intval',
                    $request->input('settings.product_ids', [])
                ));
            }

            if ($section->section_key === 'flash_sale') {
                $incoming['show_countdown'] = $request->boolean('settings.show_countdown');
            }

            if (array_key_exists('limit', $incoming)) {
                $incoming['limit'] = max(1, (int) $incoming['limit']);
            }

            $data['settings'] = array_merge($section->settings ?? [], $incoming);
        }

        $this->homepage->updateSection($id, $data);

        return back()->with('success', 'Section draft saved. Click Publish to go live.');
    }

    public function toggleSection(Request $request, int $id): RedirectResponse
    {
        $request->validate(['enabled' => 'required|boolean']);
        $this->homepage->toggleSection($id, $request->boolean('enabled'));

        return back()->with('success', 'Section draft updated. Click Publish to go live.');
    }

    public function reorderHomepage(Request $request): RedirectResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer|exists:homepage_sections,id']);
        $this->homepage->reorderSections($request->input('order'));

        return back()->with('success', 'Section order draft saved. Click Publish to go live.');
    }

    public function heroSlides(): View
    {
        return view('admin.theme.hero-slides', [
            'hero' => $this->hero->getConfig(),
            'contentLayouts' => config('themes.hero_content_layouts', []),
        ]);
    }

    public function updateHero(HeroBuilderRequest $request): RedirectResponse
    {
        try {
            $config = $this->hero->update(
                $request->validated(),
                $request->file('media_images', []) ?? [],
                $request->input('video_url'),
                $request->input('remove_media', []),
                $request->input('media_order'),
                $request->file('media_replace', []) ?? [],
                $request->input('media_video', []),
            );
        } catch (\Throwable $e) {
            report($e);

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->withErrors([
                'media_images' => 'Could not save hero media: '.$e->getMessage(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'config' => $config,
            ]);
        }

        return back()
            ->with('success', 'Hero slider draft saved. Click Publish to go live.')
            ->with('refresh_preview', true);
    }

    public function footer(): View
    {
        return view('admin.theme.footer', ['settings' => $this->footer->get()]);
    }

    public function updateFooter(FooterSettingsRequest $request): RedirectResponse
    {
        $this->footer->update($request->safe()->except('logo'), $request->file('logo'));

        return back()->with('success', 'Footer draft saved. Click Publish to go live.');
    }
}
