<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    public function upload(UploadedFile $file, string $directory, ?int $maxWidth = null, int $quality = 85, bool $preferWebp = true): string
    {
        return $this->uploadWithVariants($file, $directory, $maxWidth ? ['large' => $maxWidth] : null, $quality, $preferWebp)['path'];
    }

    /**
     * @param  array<string, int>|null  $sizes  e.g. ['thumb' => 400, 'medium' => 800, 'large' => 1200]
     * @return array{path: string, variants: array<string, string>}
     */
    public function uploadWithVariants(
        UploadedFile $file,
        string $directory,
        ?array $sizes = null,
        ?int $quality = null,
        ?bool $preferWebp = null,
    ): array {
        $quality ??= (int) config('images.quality', 85);
        $preferWebp ??= (bool) config('images.prefer_webp', true);
        $sizes ??= config('images.variants', [
            'thumb' => 400,
            'medium' => 800,
            'large' => 1200,
        ]);

        $directory = $this->normalizeDirectory($directory);

        if ($this->shouldUseImgbb()) {
            return $this->uploadToImgbb($file, $sizes, $quality, $preferWebp);
        }

        return $this->uploadLocallyWithVariants($file, $directory, $sizes, $quality, $preferWebp);
    }

    public function delete(?string $path, ?array $variants = null): void
    {
        $this->deletePath($path);

        foreach ($variants ?? [] as $variantPath) {
            $this->deletePath($variantPath);
        }
    }

    public function url(?string $path, string $size = 'large', ?array $variants = null): ?string
    {
        if ($variants && ! empty($variants[$size])) {
            $path = $variants[$size];
        }

        if (! $path) {
            return asset('images/placeholder-product.svg');
        }

        if ($this->isRemoteUrl($path)) {
            return $path;
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('images/placeholder-product.svg');
    }

    public function isRemoteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }

    private function shouldUseImgbb(): bool
    {
        $driver = (string) config('images.driver', 'auto');

        if ($driver === 'local') {
            return false;
        }

        if ($driver === 'imgbb') {
            return filled(config('images.imgbb.api_key'));
        }

        return filled(config('images.imgbb.api_key'));
    }

    private function normalizeDirectory(string $directory): string
    {
        $directory = trim($directory, '/');
        $allowed = config('images.directories', []);

        if ($allowed !== [] && ! in_array($directory, $allowed, true) && ! Str::startsWith($directory, implode('/', $allowed))) {
            foreach ($allowed as $root) {
                if (Str::startsWith($directory, $root)) {
                    return $directory;
                }
            }
        }

        return $directory;
    }

    /**
     * @param  array<string, int>  $sizes
     * @return array{path: string, variants: array<string, string>}
     */
    private function uploadToImgbb(UploadedFile $file, array $sizes, int $quality, bool $preferWebp): array
    {
        $mime = $file->getMimeType() ?? '';
        $isRaster = str_starts_with($mime, 'image/') && ! in_array($mime, ['image/svg+xml', 'image/gif'], true);

        if (! $isRaster) {
            return $this->uploadRawToImgbb($file);
        }

        $maxWidth = max($sizes ?: [1200]);
        $image = Image::read($file)->scaleDown(width: $maxWidth);
        $ext = ($preferWebp && function_exists('imagewebp')) ? 'webp' : strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $ext = 'webp';
        }

        $encoded = match ($ext) {
            'webp' => (string) $image->toWebp($quality),
            'png' => (string) $image->toPng(),
            default => (string) $image->toJpeg($quality),
        };

        $url = $this->postToImgbb($encoded, $file->getClientOriginalName());

        return [
            'path' => $url,
            'variants' => [
                'thumb' => $url,
                'medium' => $url,
                'large' => $url,
            ],
        ];
    }

    /**
     * @return array{path: string, variants: array<string, string>}
     */
    private function uploadRawToImgbb(UploadedFile $file): array
    {
        $url = $this->postToImgbb(file_get_contents($file->getRealPath()), $file->getClientOriginalName());

        return [
            'path' => $url,
            'variants' => ['thumb' => $url, 'medium' => $url, 'large' => $url],
        ];
    }

    private function postToImgbb(string $binary, string $name): string
    {
        $apiKey = config('images.imgbb.api_key');
        $apiUrl = config('images.imgbb.api_url');

        if (! $apiKey) {
            throw new \RuntimeException('ImageBB API key is not configured.');
        }

        $response = Http::timeout(60)
            ->asForm()
            ->post($apiUrl, [
                'key' => $apiKey,
                'image' => base64_encode($binary),
                'name' => Str::slug(pathinfo($name, PATHINFO_FILENAME)) ?: Str::uuid()->toString(),
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('ImageBB upload failed: '.$response->body());
        }

        $url = $response->json('data.display_url') ?? $response->json('data.url');

        if (! $url) {
            throw new \RuntimeException('ImageBB upload returned no image URL.');
        }

        return (string) $url;
    }

    /**
     * @param  array<string, int>  $sizes
     * @return array{path: string, variants: array<string, string>}
     */
    private function uploadLocallyWithVariants(
        UploadedFile $file,
        string $directory,
        array $sizes,
        int $quality,
        bool $preferWebp,
    ): array {
        $mime = $file->getMimeType() ?? '';
        $isRaster = str_starts_with($mime, 'image/') && ! in_array($mime, ['image/svg+xml', 'image/gif'], true);

        if (! $isRaster) {
            $filename = Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: 'bin');
            $stored = Storage::disk('public')->putFileAs($directory, $file, $filename);

            if (! $stored) {
                throw new \RuntimeException('Could not save file to storage. Run php artisan storage:link if needed.');
            }

            $path = $directory.'/'.$filename;

            return ['path' => $path, 'variants' => ['thumb' => $path, 'medium' => $path, 'large' => $path]];
        }

        $ext = ($preferWebp && function_exists('imagewebp')) ? 'webp' : strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $ext = 'webp';
        }

        $base = (string) Str::uuid();
        $variants = [];
        $image = Image::read($file);

        foreach ($sizes as $label => $width) {
            $filename = $label === 'large' ? "{$base}.{$ext}" : "{$base}_{$label}.{$ext}";
            $path = "{$directory}/{$filename}";
            $scaled = $image->scaleDown(width: $width);

            $encoded = match ($ext) {
                'webp' => (string) $scaled->toWebp($quality),
                'png' => (string) $scaled->toPng(),
                default => (string) $scaled->toJpeg($quality),
            };

            if (! Storage::disk('public')->put($path, $encoded)) {
                throw new \RuntimeException('Could not save image to storage. Run php artisan storage:link if needed.');
            }

            $variants[$label] = $path;
        }

        return [
            'path' => $variants['large'] ?? reset($variants),
            'variants' => $variants,
        ];
    }

    private function deletePath(?string $path): void
    {
        if (! $path || $this->isRemoteUrl($path)) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
