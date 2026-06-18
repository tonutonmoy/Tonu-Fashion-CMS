<?php

namespace App\Http\Controllers\Api;

use App\Enums\LicenseStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use App\Services\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseValidationController extends Controller
{
    public function __construct(
        private LicenseRepositoryInterface $licenses,
        private LicenseService $licenseService,
    ) {}

    public function validate(Request $request): JsonResponse
    {
        $secret = config('license.secret');

        if ($secret && $request->header('X-License-Secret') !== $secret) {
            return response()->json(['valid' => false, 'message' => 'Unauthorized.'], 401);
        }

        $data = $request->validate([
            'license_key' => ['required', 'string'],
            'license_key_hash' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'ip' => ['nullable', 'string'],
            'signature' => ['required', 'string'],
        ]);

        $license = $this->licenses->findByKeyHash($data['license_key_hash']);

        if (! $license || $license->license_key !== strtoupper($data['license_key'])) {
            return response()->json(['valid' => false, 'message' => 'License not found.']);
        }

        $domain = $this->licenseService->normalizeDomain($data['domain']);
        $expectedSignature = hash_hmac(
            'sha256',
            implode('|', [$license->license_key_hash, $domain, $data['ip'] ?? '']),
            $secret ?: (string) config('app.key')
        );

        if (! hash_equals($expectedSignature, $data['signature'])) {
            return response()->json(['valid' => false, 'message' => 'Invalid signature.']);
        }

        if ($license->licensed_domain !== $domain) {
            return response()->json(['valid' => false, 'message' => 'Domain mismatch.']);
        }

        if ($license->status === LicenseStatus::Suspended) {
            return response()->json([
                'valid' => false,
                'status' => $license->status->value,
                'expires_at' => $license->expires_at?->toIso8601String(),
                'message' => 'License suspended.',
            ]);
        }

        if ($license->isExpired() || $license->status === LicenseStatus::Expired) {
            return response()->json([
                'valid' => false,
                'status' => LicenseStatus::Expired->value,
                'expires_at' => $license->expires_at?->toIso8601String(),
                'message' => 'License expired.',
            ]);
        }

        if ($license->status !== LicenseStatus::Active) {
            return response()->json([
                'valid' => false,
                'status' => $license->status->value,
                'expires_at' => $license->expires_at?->toIso8601String(),
            ]);
        }

        $this->licenses->update($license->id, [
            'last_check_at' => now(),
            'last_ip' => $data['ip'] ?? null,
        ]);

        return response()->json([
            'valid' => true,
            'status' => $license->status->value,
            'expires_at' => $license->expires_at?->toIso8601String(),
        ]);
    }
}
