<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class StorefrontHtmlCacheMiddleware
{
    private const CSRF_PLACEHOLDER = '__STOREFRONT_CSRF__';

    private const CACHEABLE_ROUTES = [
        'home',
        'shop.index',
        'products.show',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('performance.html_cache', true) || ! $this->shouldCache($request)) {
            return $next($request);
        }

        $route = (string) $request->route()?->getName();
        $key = $this->cacheKey($request, $route);

        if ($cached = Cache::get($key)) {
            app(\App\Services\StorefrontCacheService::class)->recordCacheHit();

            return response($this->hydrateCsrf($cached))
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('X-Storefront-Cache', 'HIT');
        }

        app(\App\Services\StorefrontCacheService::class)->recordCacheMiss();

        $response = $next($request);

        if ($this->canStore($response)) {
            $content = $response->getContent();
            $token = csrf_token();

            if ($token !== '') {
                $content = str_replace($token, self::CSRF_PLACEHOLDER, $content);
            }

            Cache::put($key, $content, config('performance.cache_ttl', 3600));
            $response->headers->set('X-Storefront-Cache', 'MISS');
        }

        return $response;
    }

    private function shouldCache(Request $request): bool
    {
        if (! $request->isMethod('GET') || Auth::check() || $request->ajax()) {
            return false;
        }

        $route = $request->route()?->getName();

        if (! in_array($route, self::CACHEABLE_ROUTES, true)) {
            return false;
        }

        if ($route === 'shop.index' && ! empty($request->query())) {
            return false;
        }

        return ! $request->boolean('preview');
    }

    private function canStore(Response $response): bool
    {
        return $response->isSuccessful()
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    private function cacheKey(Request $request, string $route): string
    {
        $slug = (string) ($request->route('slug') ?? '');
        $mode = app(\App\Services\ColorModeService::class)->resolve();

        return 'storefront.html.'.$route.'.'.app()->getLocale().'.'.$mode.'.'.md5($slug);
    }

    private function hydrateCsrf(string $html): string
    {
        return str_replace(self::CSRF_PLACEHOLDER, csrf_token(), $html);
    }
}
