<?php

namespace App\Support;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PostTags
{
    public static function hydrate(Post $post): void
    {
        $post->setRelation('tags', self::forIds($post->tag_ids ?? []));
    }

    public static function hydrateMany(Collection|LengthAwarePaginator $posts): void
    {
        $items = $posts instanceof LengthAwarePaginator ? $posts->getCollection() : $posts;

        if ($items->isEmpty()) {
            return;
        }

        $allIds = $items->pluck('tag_ids')->filter()->flatten()->unique()->values()->all();
        $tagMap = $allIds === []
            ? collect()
            : Tag::query()->whereIn('id', $allIds)->get()->keyBy('id');

        foreach ($items as $post) {
            $tags = collect($post->tag_ids ?? [])
                ->map(fn ($id) => $tagMap->get($id))
                ->filter()
                ->values();

            $post->setRelation('tags', $tags);
        }
    }

    private static function forIds(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return Tag::query()->whereIn('id', $ids)->get();
    }
}
