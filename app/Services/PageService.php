<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\CmsPage;
use App\Repositories\Contracts\CmsPageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PageService
{
    public function __construct(
        private CmsPageRepositoryInterface $pages,
        private ImageService $images
    ) {}

    public function create(array $data, ?UploadedFile $banner = null, ?UploadedFile $ogImage = null): CmsPage
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);
        $data = $this->handleUploads($data, $banner, $ogImage);

        return $this->pages->create($data);
    }

    public function update(int $id, array $data, ?UploadedFile $banner = null, ?UploadedFile $ogImage = null): CmsPage
    {
        $page = $this->pages->find($id);

        if (! empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $page->id);
        } elseif (isset($data['title']) && $data['title'] !== $page->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $page->id);
        }

        $data = $this->handleUploads($data, $banner, $ogImage, $page);

        return $this->pages->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $page = $this->pages->find($id);
        $this->images->delete($page->banner_image);
        $this->images->delete($page->og_image);

        return $this->pages->delete($id);
    }

    private function handleUploads(array $data, ?UploadedFile $banner, ?UploadedFile $ogImage, ?CmsPage $existing = null): array
    {
        if ($banner) {
            if ($existing) {
                $this->images->delete($existing->banner_image);
            }
            $data['banner_image'] = $this->images->upload($banner, 'pages', 1600, 85, true);
        }

        if ($ogImage) {
            if ($existing) {
                $this->images->delete($existing->og_image);
            }
            $data['og_image'] = $this->images->upload($ogImage, 'pages', 1200, 85, true);
        }

        return $data;
    }

    private function uniqueSlug(string $value, ?int $exceptId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (CmsPage::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }
}
