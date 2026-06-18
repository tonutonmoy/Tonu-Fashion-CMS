<?php

namespace App\Concerns;

trait HasTranslations
{
    public function translated(string $field, ?string $locale = null): mixed
    {
        $locale = $locale ?? app()->getLocale();
        $translations = $this->translations ?? [];

        if (is_array($translations) && ! empty($translations[$locale][$field])) {
            return $translations[$locale][$field];
        }

        if ($locale !== 'en' && ! empty($translations['en'][$field])) {
            return $translations['en'][$field];
        }

        return $this->{$field} ?? null;
    }

    /** @param  array<string, array<string, mixed>>  $input */
    public function mergeTranslations(array $input): void
    {
        $existing = is_array($this->translations) ? $this->translations : [];

        foreach ($input as $locale => $fields) {
            if (! is_array($fields)) {
                continue;
            }

            $existing[$locale] = array_merge($existing[$locale] ?? [], array_filter($fields, fn ($v) => $v !== null && $v !== ''));
        }

        $this->translations = $existing;
    }
}
