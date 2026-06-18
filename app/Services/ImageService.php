<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    public function __construct(private ImageBbService $imageBb) {}

    public function usesRemoteStorage(): bool
    {
        $driver = config('images.driver', 'auto');

        if ($driver === 'imgbb') {
            return $this->imageBb->isConfigured();
        }

        if ($driver === 'local') {
            return false;
        }

        return $this->imageBb->isConfigured();
    }

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
        int $quality = 85,
        ?bool $preferWebp = null,
    ): array {
        $preferWebp ??= (bool) config('fashion.image.prefer_webp', true);
        $sizes ??= [
            'thumb' => (int) config('fashion.image.thumbnail_width', 400),
            'medium' => (int) config('fashion.image.medium_width', 800),
            'large' => (int) config('fashion.image.large_width', 1200),
        ];

        if ($this->usesRemoteStorage()) {
            $large = max($sizes);
            $url = $this->uploadToImageBb($file, $directory, $large, $quality, $preferWebp);

            return ['path' => $url, 'variants' => ['thumb' => $url, 'medium' => $url, 'large' => $url]];
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

        if ($this->isBrokenDemoUrl($path)) {
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

    private function isBrokenDemoUrl(string $path): bool
    {
        return str_contains($path, 'ibb.co')
            || str_contains($path, 'imgbb.com')
            || str_contains($path, 'placeholder-fashion')
            || str_contains($path, 'picsum.photos');
    }

    public function isRemoteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
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
            $stored = Storage::disk('public')->putFileAs(trim($directory, '/'), $file, $filename);

            if (! $stored) {
                throw new \RuntimeException('Could not save file to storage. Run php artisan storage:link if needed.');
            }

            $path = trim($directory, '/').'/'.$filename;

            return ['path' => $path, 'variants' => ['thumb' => $path, 'medium' => $path, 'large' => $path]];
        }

        $ext = ($preferWebp && function_exists('imagewebp')) ? 'webp' : strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $ext = 'webp';
        }

        $base = (string) Str::uuid();
        $dir = trim($directory, '/');
        $variants = [];
        $image = Image::read($file);

        foreach ($sizes as $label => $width) {
            $filename = $label === 'large' ? "{$base}.{$ext}" : "{$base}_{$label}.{$ext}";
            $path = "{$dir}/{$filename}";
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

    private function uploadToImageBb(UploadedFile $file, string $directory, ?int $maxWidth, int $quality, bool $preferWebp): string
    {
        $mime = $file->getMimeType() ?? '';
        $isRaster = str_starts_with($mime, 'image/') && $mime !== 'image/svg+xml';

        if ($isRaster && $maxWidth) {
            try {
                $image = Image::read($file)->scaleDown(width: $maxWidth);
                $binary = ($preferWebp && function_exists('imagewebp'))
                    ? (string) $image->toWebp($quality)
                    : (string) $image->toJpeg($quality);
            } catch (\Throwable) {
                $binary = (string) file_get_contents($file->getRealPath());
            }
        } else {
            $binary = (string) file_get_contents($file->getRealPath());
        }

        $name = Str::slug(str_replace('/', '-', trim($directory, '/'))) ?: 'upload';

        return $this->imageBb->upload($binary, $name);
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
