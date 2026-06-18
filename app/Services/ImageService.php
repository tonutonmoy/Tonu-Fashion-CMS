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

    public function upload(UploadedFile $file, string $directory, ?int $maxWidth = null, int $quality = 85, bool $preferWebp = false): string
    {
        if ($this->usesRemoteStorage()) {
            return $this->uploadToImageBb($file, $directory, $maxWidth, $quality, $preferWebp);
        }

        return $this->uploadLocally($file, $directory, $maxWidth, $quality, $preferWebp);
    }

    public function delete(?string $path): void
    {
        if (! $path || $this->isRemoteUrl($path)) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function url(?string $path): ?string
    {
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

        return Storage::disk('public')->url($path);
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

    private function uploadLocally(UploadedFile $file, string $directory, ?int $maxWidth, int $quality, bool $preferWebp): string
    {
        $mime = $file->getMimeType() ?? '';
        $isRaster = str_starts_with($mime, 'image/') && ! in_array($mime, ['image/svg+xml', 'image/gif'], true);

        if (! $isRaster) {
            $filename = Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: 'bin');
            $stored = Storage::disk('public')->putFileAs(trim($directory, '/'), $file, $filename);

            if (! $stored) {
                throw new \RuntimeException('Could not save file to storage. Run php artisan storage:link if needed.');
            }

            return trim($directory, '/').'/'.$filename;
        }

        $originalExt = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $ext = ($preferWebp && function_exists('imagewebp')) ? 'webp' : $originalExt;
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'jpg';
        }

        $filename = Str::uuid().'.'.$ext;
        $path = trim($directory, '/').'/'.$filename;
        $image = Image::read($file);

        if ($maxWidth) {
            $image = $image->scaleDown(width: $maxWidth);
        }

        $encoded = match ($ext) {
            'webp' => (string) $image->toWebp($quality),
            'png' => (string) $image->toPng(),
            'gif' => (string) $image->toGif(),
            default => (string) $image->toJpeg($quality),
        };

        if (! Storage::disk('public')->put($path, $encoded)) {
            throw new \RuntimeException('Could not save image to storage. Run php artisan storage:link if needed.');
        }

        return $path;
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
}
