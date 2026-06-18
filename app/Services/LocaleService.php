<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class LocaleService
{
    public function supported(): array
    {
        return config('locales.supported', ['en', 'bn']);
    }

    public function default(): string
    {
        return setting('default_locale', config('locales.default', 'en'));
    }

    public function current(): string
    {
        return App::getLocale();
    }

    public function resolve(?string $fromRequest = null): string
    {
        $locale = $fromRequest
            ?? request()->cookie('app_locale')
            ?? $this->default();

        if (! in_array($locale, $this->supported(), true)) {
            $locale = $this->default();
        }

        return $locale;
    }

    public function apply(?string $locale = null): string
    {
        $locale = $this->resolve($locale);
        App::setLocale($locale);

        return $locale;
    }

    public function label(string $locale): string
    {
        return config("locales.labels.{$locale}", strtoupper($locale));
    }
}
