<?php

namespace App\Services;

use App\Data\LicenseValidationResult;
use App\Enums\LicenseStatus;
use App\Models\License;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LicenseService
{
    private ?License $resolvedLicense = null;

    private ?LicenseValidationResult $lastValidation = null;

    public function __construct(private LicenseRepositoryInterface $licenses) {}

    public function generateKey(): string
    {
        do {
            $key = sprintf(
                'FBD-%s-%s-%s',
                strtoupper(Str::random(4)),
                strtoupper(Str::random(4)),
                now()->format('Y')
            );
        } while ($this->licenses->findByKey($key));

        return $key;
    }

    public function hashKey(string $licenseKey): string
    {
        return hash_hmac('sha256', strtoupper(trim($licenseKey)), $this->signingSecret());
    }

    public function createLicense(array $data): License
    {
        $key = $data['license_key'] ?? $this->generateKey();
        $domain = ! empty($data['licensed_domain'])
            ? $this->normalizeDomain($data['licensed_domain'])
            : null;

        $license = $this->licenses->create([
            'license_key' => strtoupper($key),
            'license_key_hash' => $this->hashKey($key),
            'licensed_domain' => $domain,
            'customer_name' => $data['customer_name'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'plan' => $data['plan'] ?? 'standard',
            'issued_at' => $data['issued_at'] ?? now(),
            'expires_at' => $data['expires_at'] ?? null,
            'status' => LicenseStatus::Active,
            'notes' => $data['notes'] ?? null,
        ]);

        return $this->refreshSignature($license);
    }

    public function updateLicense(int $id, array $data): License
    {
        if (isset($data['licensed_domain'])) {
            $data['licensed_domain'] = $data['licensed_domain']
                ? $this->normalizeDomain($data['licensed_domain'])
                : null;
        }

        if (isset($data['status'])) {
            $data['status'] = $data['status'] instanceof LicenseStatus
                ? $data['status']
                : LicenseStatus::from($data['status']);
        }

        $license = $this->licenses->update($id, $data);

        return $this->refreshSignature($license);
    }

    public function assignDomain(int $id, string $domain): License
    {
        $normalized = $this->normalizeDomain($domain);

        $license = $this->licenses->update($id, [
            'licensed_domain' => $normalized,
        ]);

        $this->clearValidationCache($normalized);

        return $this->refreshSignature($license);
    }

    public function suspend(int $id): License
    {
        $license = $this->licenses->update($id, ['status' => LicenseStatus::Suspended]);
        $this->clearValidationCache($license->licensed_domain);

        return $this->refreshSignature($license);
    }

    public function expire(int $id): License
    {
        $license = $this->licenses->update($id, [
            'status' => LicenseStatus::Expired,
            'expires_at' => now(),
        ]);
        $this->clearValidationCache($license->licensed_domain);

        return $this->refreshSignature($license);
    }

    public function activate(int $id): License
    {
        $license = $this->licenses->update($id, ['status' => LicenseStatus::Active]);
        $this->clearValidationCache($license->licensed_domain);

        return $this->refreshSignature($license);
    }

    public function validate(?Request $request = null): LicenseValidationResult
    {
        $request = $request ?? request();
        $domain = $this->normalizeDomain($request->getHost());

        if ($this->shouldSkipValidation($domain)) {
            return LicenseValidationResult::valid(new License([
                'license_key' => 'LOCAL-DEV',
                'licensed_domain' => $domain,
                'status' => LicenseStatus::Active,
            ]));
        }

        $cacheKey = $this->cacheKey($domain);

        if ($cached = Cache::get($cacheKey)) {
            $this->lastValidation = $cached instanceof LicenseValidationResult
                ? $cached
                : LicenseValidationResult::invalid('Invalid cache payload.');

            if ($this->lastValidation->license) {
                $this->resolvedLicense = $this->lastValidation->license;
            }

            return $this->lastValidation;
        }

        $result = $this->performValidation($domain, $request->ip());
        Cache::put($cacheKey, $result, config('license.cache_ttl', 86400));

        $this->lastValidation = $result;

        if ($result->license) {
            $this->resolvedLicense = $result->license;
        }

        return $result;
    }

    public function isValid(?Request $request = null): bool
    {
        return $this->validate($request)->valid;
    }

    public function current(?Request $request = null): ?License
    {
        if ($this->resolvedLicense) {
            return $this->resolvedLicense;
        }

        $this->validate($request);

        return $this->resolvedLicense;
    }

    public function licensedDomain(?Request $request = null): ?string
    {
        return $this->current($request)?->licensed_domain;
    }

    public function stats(): array
    {
        return $this->licenses->stats();
    }

    public function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = explode(':', $domain)[0];

        return $domain;
    }

    public function buildSignature(License $license): string
    {
        $payload = implode('|', [
            $license->license_key_hash,
            $license->licensed_domain ?? '',
            $license->expires_at?->timestamp ?? 0,
            $license->status->value,
        ]);

        return hash_hmac('sha256', $payload, $this->signingSecret());
    }

    public function verifySignature(License $license): bool
    {
        if (! $license->verification_signature) {
            return false;
        }

        return hash_equals($license->verification_signature, $this->buildSignature($license));
    }

    public function refreshSignature(License $license): License
    {
        $signature = $this->buildSignature($license);

        return $this->licenses->update($license->id, [
            'verification_signature' => $signature,
        ]);
    }

    private function performValidation(string $domain, ?string $ip): LicenseValidationResult
    {
        $license = $this->licenses->findByDomain($domain);

        if (! $license) {
            return LicenseValidationResult::invalid(
                'No license found for this domain.',
                'invalid'
            );
        }

        if (! $this->verifySignature($license)) {
            $this->licenses->update($license->id, ['status' => LicenseStatus::Invalid]);

            return LicenseValidationResult::invalid(
                'License verification failed. Data may have been tampered with.',
                'invalid'
            );
        }

        if ($license->status === LicenseStatus::Suspended) {
            return LicenseValidationResult::suspended($license, 'This license has been suspended.');
        }

        if ($license->status === LicenseStatus::Invalid) {
            return LicenseValidationResult::invalid('This license is invalid.');
        }

        if ($license->isExpired() || $license->status === LicenseStatus::Expired) {
            if ($license->status !== LicenseStatus::Expired) {
                $license = $this->licenses->update($license->id, ['status' => LicenseStatus::Expired]);
            }

            return LicenseValidationResult::expired($license, 'Your license has expired. Please renew to continue.');
        }

        if ($license->status !== LicenseStatus::Active) {
            return LicenseValidationResult::invalid('License status is not active.');
        }

        $remote = $this->validateRemotely($license, $domain, $ip);

        if (! $remote['valid']) {
            return LicenseValidationResult::invalid($remote['message'] ?? 'Remote license validation failed.');
        }

        if (! empty($remote['expires_at'])) {
            $expiresAt = \Carbon\Carbon::parse($remote['expires_at']);
            if ($expiresAt->isPast()) {
                $license = $this->licenses->update($license->id, ['status' => LicenseStatus::Expired, 'expires_at' => $expiresAt]);

                return LicenseValidationResult::expired($license, 'Your license has expired.');
            }
        }

        if (! empty($remote['status']) && $remote['status'] !== LicenseStatus::Active->value) {
            $status = LicenseStatus::tryFrom($remote['status']) ?? LicenseStatus::Invalid;
            $license = $this->licenses->update($license->id, ['status' => $status]);

            return LicenseValidationResult::invalid('License status updated by license server.');
        }

        $license = $this->licenses->update($license->id, [
            'last_check_at' => now(),
            'last_ip' => $ip,
        ]);

        return LicenseValidationResult::valid($license);
    }

    private function validateRemotely(License $license, string $domain, ?string $ip): array
    {
        $url = config('license.server_url');

        if (! $url) {
            return ['valid' => true];
        }

        try {
            $payload = [
                'license_key' => $license->license_key,
                'license_key_hash' => $license->license_key_hash,
                'domain' => $domain,
                'ip' => $ip,
                'signature' => $this->buildRemoteSignature($license, $domain, $ip),
            ];

            $response = Http::timeout(15)
                ->withHeaders(['X-License-Secret' => config('license.secret')])
                ->post(rtrim($url, '/').'/validate', $payload);

            if (! $response->successful()) {
                Log::warning('License remote validation HTTP error', ['status' => $response->status()]);

                return ['valid' => true, 'message' => 'Remote check skipped due to server error.'];
            }

            return $response->json() ?? ['valid' => false, 'message' => 'Invalid remote response.'];
        } catch (\Throwable $e) {
            Log::warning('License remote validation failed', ['error' => $e->getMessage()]);

            return ['valid' => true, 'message' => 'Remote check unavailable.'];
        }
    }

    private function buildRemoteSignature(License $license, string $domain, ?string $ip): string
    {
        $payload = implode('|', [$license->license_key_hash, $domain, $ip ?? '']);

        return hash_hmac('sha256', $payload, config('license.secret', $this->signingSecret()));
    }

    private function shouldSkipValidation(string $domain): bool
    {
        if (! config('license.skip_local')) {
            return false;
        }

        return in_array($domain, ['localhost', '127.0.0.1', '::1'], true)
            || str_ends_with($domain, '.test')
            || str_ends_with($domain, '.local');
    }

    private function signingSecret(): string
    {
        return config('license.secret') ?: (string) config('app.key');
    }

    private function cacheKey(string $domain): string
    {
        return 'license_validation_'.md5($domain);
    }

    private function clearValidationCache(?string $domain): void
    {
        if ($domain) {
            Cache::forget($this->cacheKey($this->normalizeDomain($domain)));
        }
    }
}
