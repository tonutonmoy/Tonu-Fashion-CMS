<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public function log(string $action, ?string $description = null, ?Model $subject = null, array $properties = []): ActivityLog
    {
        return ActivityLog::query()->create([
            'user_id' => Auth::id(),
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
        ]);
    }

    public function recent(int $limit = 20)
    {
        return ActivityLog::query()->with('user')->latest()->limit($limit)->get();
    }

    public function paginateAdmin(?int $perPage = null)
    {
        $perPage ??= admin_per_page();

        return ActivityLog::query()->with('user')->latest()->paginate($perPage)->withQueryString();
    }
}
