<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(private BackupService $backups) {}

    public function index(): View
    {
        return view('admin.backup.index', [
            'backups' => $this->backups->list(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => ['accepted'],
        ]);

        try {
            $filename = $this->backups->create();

            return back()->with('success', "Backup created: {$filename}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: '.$e->getMessage());
        }
    }

    public function download(string $filename): BinaryFileResponse
    {
        $path = $this->backups->downloadPath($filename);

        return response()->download($path);
    }

    public function restore(Request $request, string $filename): RedirectResponse
    {
        $request->validate([
            'confirm_restore' => ['accepted'],
        ]);

        try {
            $this->backups->restore($filename);

            return back()->with('success', "Restored from {$filename}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore failed: '.$e->getMessage());
        }
    }

    public function destroy(string $filename): RedirectResponse
    {
        try {
            $this->backups->delete($filename);

            return back()->with('success', 'Backup deleted.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
