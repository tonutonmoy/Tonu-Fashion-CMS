<?php

namespace App\Services;

use App\Enums\RecordStatus;
use App\Models\BuilderDraft;
use App\Models\FooterSetting;
use App\Models\HeroSlide;
use App\Models\HomepageSection;
use App\Models\ThemeSetting;
use App\Repositories\Contracts\FooterSettingRepositoryInterface;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use App\Repositories\Contracts\HomepageSectionRepositoryInterface;
use App\Repositories\Contracts\ThemeSettingRepositoryInterface;
use App\Services\StorefrontCacheService;
use Illuminate\Database\Eloquent\Collection;

class BuilderPublishService
{
    public function __construct(
        private ThemeSettingRepositoryInterface $themeSettings,
        private HomepageSectionRepositoryInterface $sections,
        private HeroSlideRepositoryInterface $slides,
        private FooterSettingRepositoryInterface $footer,
        private ImageService $images,
    ) {}

    public function hasUnpublishedChanges(): bool
    {
        return (bool) $this->getDraftRecord()?->has_changes;
    }

    public function getEffectiveThemeSettings(): ThemeSetting
    {
        $published = $this->themeSettings->get();
        $draft = $this->getDraftRecord()?->theme;

        if (empty($draft)) {
            return $published;
        }

        $model = $published->replicate();
        $model->fill(array_merge($published->toArray(), $draft));
        $model->id = $published->id;

        return $model;
    }

    public function setDraftTheme(array $data): void
    {
        $record = $this->getOrCreateDraftRecord();
        $base = $record->theme ?? $this->themeSettings->get()->only((new ThemeSetting)->getFillable());
        $record->theme = array_merge($base, $data);
        $record->has_changes = true;
        $record->save();
    }

    public function getEffectiveFooterSettings(): FooterSetting
    {
        $published = $this->footer->get();
        $draft = $this->getDraftRecord()?->footer;

        if (empty($draft)) {
            return $published;
        }

        $model = $published->replicate();
        $model->fill(array_merge($published->toArray(), $draft));
        $model->id = $published->id;

        return $model;
    }

    public function setDraftFooter(array $data): void
    {
        $record = $this->getOrCreateDraftRecord();
        $base = $record->footer ?? $this->footer->get()->only((new FooterSetting)->getFillable());
        $record->footer = array_merge($base, $data);
        $record->has_changes = true;
        $record->save();
    }

    public function getEffectiveHomepageSections(): Collection
    {
        return $this->hydrateHomepageSections($this->homepageDraftList());
    }

    public function updateHomepageSection(int $id, array $data): void
    {
        $list = $this->homepageDraftList();

        foreach ($list as $index => $section) {
            if ((int) $section['id'] !== $id) {
                continue;
            }

            if (isset($data['settings']) && is_array($data['settings'])) {
                $data['settings'] = array_merge($section['settings'] ?? [], $data['settings']);
            }

            $list[$index] = array_merge($section, $data);
            break;
        }

        $this->persistHomepageDraft($list);
    }

    public function toggleHomepageSection(int $id, bool $enabled): void
    {
        $this->updateHomepageSection($id, ['enabled' => $enabled]);
    }

    public function reorderHomepageSections(array $orderedIds): void
    {
        $list = collect($this->homepageDraftList())->keyBy('id');

        foreach ($orderedIds as $order => $id) {
            if ($list->has($id)) {
                $row = $list->get($id);
                $row['sort_order'] = $order + 1;
                $list->put($id, $row);
            }
        }

        $this->persistHomepageDraft($list->values()->sortBy('sort_order')->values()->all());
    }

    public function getEffectiveHeroSlides(): Collection
    {
        return $this->hydrateHeroSlides($this->heroSlidesDraftList());
    }

    public function storeHeroSlide(array $data): void
    {
        $list = $this->heroSlidesDraftList();
        $data['id'] = $this->nextTempHeroSlideId($list);
        $data['sort_order'] = $data['sort_order'] ?? (collect($list)->max('sort_order') + 1);
        $data['status'] = $data['status'] ?? RecordStatus::Active->value;
        $data['content_layout'] = $data['content_layout'] ?? 'centered';
        $data['title_size'] = $data['title_size'] ?? 40;
        $data['subtitle_size'] = $data['subtitle_size'] ?? 18;
        $data['button_size'] = $data['button_size'] ?? 14;
        $list[] = $data;

        $this->persistHeroSlidesDraft($list);
    }

    public function updateHeroSlide(int $id, array $data): void
    {
        $list = $this->heroSlidesDraftList();

        foreach ($list as $index => $slide) {
            if ((int) $slide['id'] !== $id) {
                continue;
            }

            $list[$index] = array_merge($slide, $data);
            break;
        }

        $this->persistHeroSlidesDraft($list);
    }

    public function deleteHeroSlide(int $id): ?array
    {
        $list = $this->heroSlidesDraftList();
        $removed = null;

        foreach ($list as $index => $slide) {
            if ((int) $slide['id'] !== $id) {
                continue;
            }

            $removed = $slide;
            unset($list[$index]);
            break;
        }

        $this->persistHeroSlidesDraft(array_values($list));

        return $removed;
    }

    public function reorderHeroSlides(array $orderedIds): void
    {
        $list = collect($this->heroSlidesDraftList())->keyBy('id');

        foreach ($orderedIds as $order => $id) {
            if ($list->has((int) $id)) {
                $row = $list->get((int) $id);
                $row['sort_order'] = $order + 1;
                $list->put((int) $id, $row);
            }
        }

        $this->persistHeroSlidesDraft($list->values()->sortBy('sort_order')->values()->all());
    }

    public function findHeroSlide(int $id): ?HeroSlide
    {
        return $this->getEffectiveHeroSlides()->firstWhere('id', $id);
    }

    public function publish(): void
    {
        $record = $this->getDraftRecord();

        if (! $record?->has_changes) {
            return;
        }

        $bumpAssets = false;

        if (! empty($record->theme)) {
            $this->themeSettings->update($record->theme);
            $bumpAssets = true;
        }

        if ($record->homepage !== null) {
            $this->publishHomepage($record->homepage);
            $bumpAssets = true;
        }

        if ($record->hero_slides !== null) {
            $this->publishHeroSlides($record->hero_slides);
            $bumpAssets = true;
        }

        if (! empty($record->footer)) {
            $this->footer->update($record->footer);
            $bumpAssets = true;
        }

        if ($bumpAssets) {
            $this->themeSettings->bumpAssetVersion();
        }

        $record->update([
            'has_changes' => false,
            'theme' => null,
            'homepage' => null,
            'hero_slides' => null,
            'footer' => null,
        ]);

        app(StorefrontCacheService::class)->forgetAll();
    }

    private function publishHomepage(array $sections): void
    {
        foreach ($sections as $section) {
            $id = (int) ($section['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $this->sections->update($id, [
                'enabled' => (bool) ($section['enabled'] ?? true),
                'sort_order' => (int) ($section['sort_order'] ?? 0),
                'settings' => $section['settings'] ?? [],
            ]);
        }
    }

    private function publishHeroSlides(array $draftSlides): void
    {
        $publishedIds = $this->slides->all()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $draftIds = collect($draftSlides)
            ->pluck('id')
            ->filter(fn ($id) => (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach (array_diff($publishedIds, $draftIds) as $id) {
            $slide = $this->slides->find($id);
            if ($slide) {
                $this->images->delete($slide->desktop_image);
                $this->images->delete($slide->mobile_image);
                $this->slides->delete($id);
            }
        }

        foreach ($draftSlides as $order => $row) {
            $payload = $row;
            $payload['sort_order'] = $order + 1;

            if (isset($payload['status']) && $payload['status'] instanceof RecordStatus) {
                $payload['status'] = $payload['status']->value;
            }

            unset($payload['created_at'], $payload['updated_at']);

            $id = (int) ($payload['id'] ?? 0);
            unset($payload['id']);

            if ($id > 0) {
                $this->slides->update($id, $payload);
            } else {
                $this->slides->create($payload);
            }
        }
    }

    private function homepageDraftList(): array
    {
        $record = $this->getDraftRecord();

        if ($record && $record->homepage !== null) {
            return $record->homepage;
        }

        return $this->sections->all()
            ->sortBy('sort_order')
            ->map(fn (HomepageSection $section) => $section->toArray())
            ->values()
            ->all();
    }

    private function heroSlidesDraftList(): array
    {
        $record = $this->getDraftRecord();

        if ($record && $record->hero_slides !== null) {
            return $this->normalizeHeroSlideRows($record->hero_slides);
        }

        return $this->slides->all()
            ->sortBy('sort_order')
            ->map(fn (HeroSlide $slide) => $this->heroSlideToArray($slide))
            ->values()
            ->all();
    }

    private function normalizeHeroSlideRows(array $rows): array
    {
        $published = $this->slides->all()->keyBy('id');

        return collect($rows)->map(function (array $row) use ($published) {
            $id = (int) ($row['id'] ?? 0);

            if ($id > 0 && $published->has($id)) {
                $live = $published->get($id);

                foreach (['content_layout', 'title_size', 'subtitle_size', 'button_size', 'overlay_color', 'video_url'] as $field) {
                    if (! array_key_exists($field, $row) || $row[$field] === null || $row[$field] === '') {
                        $value = $live->{$field};
                        $row[$field] = $value;
                    }
                }
            }

            $row['content_layout'] = $row['content_layout'] ?? 'centered';

            return $row;
        })->values()->all();
    }

    private function persistHomepageDraft(array $list): void
    {
        $record = $this->getOrCreateDraftRecord();
        $record->homepage = $list;
        $record->has_changes = true;
        $record->save();
    }

    private function persistHeroSlidesDraft(array $list): void
    {
        $record = $this->getOrCreateDraftRecord();
        $record->hero_slides = $list;
        $record->has_changes = true;
        $record->save();
    }

    private function hydrateHomepageSections(array $list): Collection
    {
        $sections = collect($list)->map(function (array $row) {
            $section = new HomepageSection($row);
            $section->id = (int) $row['id'];
            $section->exists = true;

            return $section;
        })->sortBy('sort_order')->values()->all();

        return new Collection($sections);
    }

    private function hydrateHeroSlides(array $list): Collection
    {
        $slides = collect($list)->map(function (array $row) {
            $slide = new HeroSlide($row);
            $slide->id = (int) $row['id'];
            $slide->exists = true;

            if (isset($row['status'])) {
                $slide->status = $row['status'] instanceof RecordStatus
                    ? $row['status']
                    : RecordStatus::from($row['status']);
            }

            return $slide;
        })->sortBy('sort_order')->values()->all();

        return new Collection($slides);
    }

    private function heroSlideToArray(HeroSlide $slide): array
    {
        $data = $slide->toArray();
        $data['status'] = $slide->status instanceof RecordStatus
            ? $slide->status->value
            : $slide->status;

        return $data;
    }

    private function nextTempHeroSlideId(array $slides): int
    {
        $minId = collect($slides)->min('id') ?? 0;

        return min((int) $minId, 0) - 1;
    }

    private function getDraftRecord(): ?BuilderDraft
    {
        return BuilderDraft::query()->first();
    }

    private function getOrCreateDraftRecord(): BuilderDraft
    {
        return BuilderDraft::query()->firstOrCreate([], ['has_changes' => false]);
    }
}
