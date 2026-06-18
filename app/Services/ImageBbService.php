<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageBbService
{
    public function isConfigured(): bool
    {
        return filled(config('images.imgbb.api_key'));
    }

    public function upload(string $binary, string $name = 'upload'): string
    {
        $payload = [
            'key' => config('images.imgbb.api_key'),
            'image' => base64_encode($binary),
            'name' => $name,
        ];

        if ($expiration = config('images.imgbb.expiration')) {
            $payload['expiration'] = (int) $expiration;
        }

        $response = Http::timeout(30)
            ->asForm()
            ->post(config('images.imgbb.endpoint'), $payload);

        if (! $response->successful()) {
            Log::error('ImageBB upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('ImageBB upload failed. Please try again.');
        }

        $url = $response->json('data.url');

        if (! $url) {
            throw new \RuntimeException('ImageBB returned an invalid response.');
        }

        return $url;
    }
}
