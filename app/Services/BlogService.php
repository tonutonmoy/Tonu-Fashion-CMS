<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\Post;
use App\Models\Tag;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogService
{
    public function __construct(
        private PostRepositoryInterface $posts,
        private ImageService $images
    ) {}

    public function create(array $data, ?UploadedFile $featured = null, array $tagNames = []): Post
    {
        return DB::transaction(function () use ($data, $featured, $tagNames) {
            $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);

            if ($featured) {
                $data['featured_image'] = $this->images->upload($featured, 'blog', 1200, 85, true);
            }

            if (($data['status'] ?? '') === ContentStatus::Published->value && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $data['translations'] = $this->normalizeTranslations($data['translations'] ?? null);

            $post = $this->posts->create($data);
            $this->syncTags($post, $tagNames);

            return $post->load(['category']);
        });
    }

    public function update(int $id, array $data, ?UploadedFile $featured = null, array $tagNames = []): Post
    {
        return DB::transaction(function () use ($id, $data, $featured, $tagNames) {
            $post = $this->posts->find($id);

            if (! empty($data['slug'])) {
                $data['slug'] = $this->uniqueSlug($data['slug'], $post->id);
            }

            if ($featured) {
                $this->images->delete($post->featured_image);
                $data['featured_image'] = $this->images->upload($featured, 'blog', 1200, 85, true);
            }

            if (($data['status'] ?? $post->status->value) === ContentStatus::Published->value && ! $post->published_at) {
                $data['published_at'] = now();
            }

            if (array_key_exists('translations', $data)) {
                $data['translations'] = $this->normalizeTranslations($data['translations'], $post->translations);
            }

            $post = $this->posts->update($id, $data);
            $this->syncTags($post, $tagNames);

            return $post->load(['category']);
        });
    }

    public function delete(int $id): bool
    {
        $post = $this->posts->find($id);
        $this->images->delete($post->featured_image);
        $this->images->delete($post->og_image);

        return $this->posts->delete($id);
    }

    private function syncTags(Post $post, array $tagNames): void
    {
        $ids = collect($tagNames)
            ->filter()
            ->map(fn ($name) => Tag::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            )->id)
            ->all();

        $post->update(['tag_ids' => $ids]);
    }

    private function uniqueSlug(string $value, ?int $exceptId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (Post::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }

    private function normalizeTranslations(?array $incoming, ?array $existing = null): ?array
    {
        if ($incoming === null) {
            return $existing;
        }

        $normalized = is_array($existing) ? $existing : [];

        foreach ($incoming as $locale => $fields) {
            if (! is_array($fields)) {
                continue;
            }

            $clean = array_filter($fields, fn ($v) => $v !== null && $v !== '');
            if ($clean === []) {
                unset($normalized[$locale]);
            } else {
                $normalized[$locale] = array_merge($normalized[$locale] ?? [], $clean);
            }
        }

        return $normalized === [] ? null : $normalized;
    }
}
