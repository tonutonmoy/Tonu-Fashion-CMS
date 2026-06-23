<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function __construct(
        private AdminNotificationService $notifications,
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->canAdmin('store'), 403);

        return response()->json($this->notifications->payload($request->user()));
    }

    public function markRead(Request $request): JsonResponse
    {
        abort_unless($request->user()?->canAdmin('store'), 403);

        $request->validate([
            'type' => ['required', 'in:low_stock'],
        ]);

        if ($request->type === 'low_stock') {
            $this->notifications->markLowStockRead($request->user());
        }

        return response()->json([
            'ok' => true,
            ...$this->notifications->payload($request->user()),
        ]);
    }
}
