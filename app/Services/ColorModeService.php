<?php

namespace App\Services;

class ColorModeService
{
    public function supported(): array
    {
        return config('locales.color_modes', ['light', 'dark']);
    }

    public function default(): string
    {
        $mode = setting('default_color_mode', config('locales.default_color_mode', 'light'));

        return in_array($mode, $this->supported(), true) ? $mode : 'light';
    }

    public function resolve(?string $fromRequest = null): string
    {
        $mode = $fromRequest
            ?? request()->cookie('color_mode')
            ?? $this->default();

        return in_array($mode, $this->supported(), true) ? $mode : 'light';
    }
}
