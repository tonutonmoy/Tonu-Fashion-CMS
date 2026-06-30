<?php

namespace App\Http\Controllers;

use App\Services\ColorModeService;
use App\Services\LocaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function locale(Request $request, string $locale, LocaleService $locales): RedirectResponse
    {
        if (! in_array($locale, $locales->supported(), true)) {
            abort(404);
        }

        return redirect()->to($this->safeBackUrl($request))
            ->withCookie(cookie('app_locale', $locale, 60 * 24 * 365));
    }

    public function colorMode(Request $request, string $mode, ColorModeService $colorModes): RedirectResponse
    {
        if (! in_array($mode, $colorModes->supported(), true)) {
            abort(404);
        }

        return redirect()->to($this->safeBackUrl($request))
            ->withCookie(cookie('color_mode', $mode, 60 * 24 * 365));
    }

    private function safeBackUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');
        $appRoot = rtrim((string) config('app.url'), '/');

        if ($referer && str_starts_with($referer, $appRoot)) {
            return $referer;
        }

        $previous = url()->previous();
        if ($previous && $previous !== url()->current()) {
            return $previous;
        }

        return route('shop.index');
    }
}
