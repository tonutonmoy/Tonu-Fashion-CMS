<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    public function __construct(private LicenseService $licenses) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        $result = $this->licenses->validate($request);

        if ($result->valid) {
            return $next($request);
        }

        if ($result->isExpired()) {
            return response()->view('license.expired', [
                'license' => $result->license,
                'message' => $result->message,
                'provider' => config('license'),
            ], 403);
        }

        return response()->view('license.invalid', [
            'license' => $result->license,
            'message' => $result->message ?? 'This installation is not licensed for this domain.',
            'domain' => $request->getHost(),
            'provider' => config('license'),
        ], 403);
    }

    private function shouldBypass(Request $request): bool
    {
        if ($request->is('install', 'install/*', 'up')) {
            return true;
        }

        if ($request->is('api/license/validate')) {
            return true;
        }

        if ($request->is('payments/callback/*', 'payments/ipn/*')) {
            return true;
        }

        if ($request->is('admin/login') || $request->routeIs('admin.login')) {
            return true;
        }

        if ($request->is('admin/license', 'admin/license/*')) {
            return true;
        }

        return false;
    }
}
