@extends('layouts.admin')
@section('title', 'Backups')
@section('content')
<div class="max-w-5xl">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Backups</h1>
            <p class="text-sm text-gray-600 mt-1">Download full database + media archives. Compatible with Hostinger, cPanel, and manual off-site storage.</p>
        </div>
        <form action="{{ route('admin.backup.store') }}" method="POST" class="flex items-end gap-3">
            @csrf
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="confirm" value="1" required class="rounded border-gray-300">
                Create new backup
            </label>
            <button type="submit" class="btn-primary">Run Backup</button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">File</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Size</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Created</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($backups as $backup)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $backup['name'] }}</td>
                        <td class="px-4 py-3">{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                        <td class="px-4 py-3">{{ $backup['created_at'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.backup.download', $backup['name']) }}" class="btn-secondary text-xs px-3 py-1.5">Download</a>
                                <form action="{{ route('admin.backup.restore', $backup['name']) }}" method="POST" onsubmit="return confirm('Restore will overwrite current database and media. Continue?');">
                                    @csrf
                                    <input type="hidden" name="confirm_restore" value="1">
                                    <button type="submit" class="btn-secondary text-xs px-3 py-1.5">Restore</button>
                                </form>
                                <form action="{{ route('admin.backup.destroy', $backup['name']) }}" method="POST" onsubmit="return confirm('Delete this backup?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No backups yet. Create your first backup above.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 rounded-lg bg-gray-50 border border-gray-200 p-4 text-sm text-gray-600">
        <p class="font-semibold text-gray-800 mb-2">What is included</p>
        <ul class="list-disc pl-5 space-y-1">
            <li><strong>database.sql</strong> — mysqldump when available, otherwise Laravel export</li>
            <li><strong>storage.zip</strong> — all uploaded media from <code>storage/app/public</code></li>
            <li>Final archive: <code>backup-YYYY-MM-DD-HHMMSS.zip</code></li>
        </ul>
    </div>
</div>
@endsection
