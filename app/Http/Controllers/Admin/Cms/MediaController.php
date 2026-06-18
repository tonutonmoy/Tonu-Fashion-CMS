<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaUploadRequest;
use App\Services\MediaLibraryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private MediaLibraryService $media) {}

    public function index(Request $request): View
    {
        return view('admin.cms.media.index', [
            'media' => $this->media->paginate($request->all()),
            'folders' => config('cms.media_folders', []),
        ]);
    }

    public function store(MediaUploadRequest $request): RedirectResponse|JsonResponse
    {
        $item = $this->media->upload($request->file('file'), $request->input('folder', 'uploads'));

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $item->id,
                'url' => $item->url,
                'filename' => $item->filename,
            ]);
        }

        return back()->with('success', 'File uploaded.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->media->delete($id);

        return back()->with('success', 'Media deleted.');
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:1']);

        return response()->json(
            $this->media->search($request->input('q'))->map(fn ($m) => [
                'id' => $m->id,
                'url' => $m->url,
                'filename' => $m->filename,
                'folder' => $m->folder,
            ])
        );
    }
}
