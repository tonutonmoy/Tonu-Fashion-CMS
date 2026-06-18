<?php

namespace App\Services;

use App\Models\Media;
use App\Repositories\Contracts\MediaRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;

class MediaLibraryService
{
    public function __construct(
        private MediaRepositoryInterface $media,
        private ImageService $images
    ) {}

    public function upload(UploadedFile $file, string $folder = 'uploads'): Media
    {
        $mime = $file->getMimeType() ?? 'application/octet-stream';
        $isImage = str_starts_with($mime, 'image/') && $mime !== 'image/svg+xml';

        $path = $isImage
            ? $this->images->upload($file, $folder, 1600, 85, true)
            : $this->images->upload($file, $folder);

        $width = null;
        $height = null;

        if ($isImage && ! $this->images->isRemoteUrl($path)) {
            try {
                $image = Image::read($file);
                $width = $image->width();
                $height = $image->height();
            } catch (\Throwable) {
                //
            }
        }

        return $this->media->create([
            'folder' => $folder,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $mime,
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function delete(int $id): bool
    {
        $item = $this->media->find($id);
        $this->images->delete($item->path);

        return $this->media->delete($id);
    }

    public function paginate(array $filters = [], int $perPage = 24)
    {
        return $this->media->paginateAdmin($filters, $perPage);
    }

    public function search(string $query, int $limit = 50)
    {
        return $this->media->search($query, $limit);
    }
}
