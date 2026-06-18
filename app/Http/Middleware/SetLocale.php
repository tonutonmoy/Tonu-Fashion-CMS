<?php

namespace App\Http\Middleware;

use App\Services\ColorModeService;
use App\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(
        private LocaleService $locale,
        private ColorModeService $colorMode,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->locale->apply();
        view()->share('currentLocale', $this->locale->current());
        view()->share('supportedLocales', $this->locale->supported());
        view()->share('colorMode', $this->colorMode->resolve());

        return $next($request);
    }
}
