<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportMessageRequest;
use App\Models\SupportConversation;
use App\Services\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportChatController extends Controller
{
    public function __construct(private SupportChatService $chat) {}

    public function index(Request $request): View
    {
        return view('admin.support.index', [
            'conversations' => $this->chat->inbox($request->get('status', 'open')),
            'unreadCount' => $this->chat->totalAdminUnread(),
        ]);
    }

    public function show(SupportConversation $conversation): View
    {
        $conversation->load(['messages.sender:id,name']);
        $this->chat->markReadByAdmin($conversation);

        return view('admin.support.show', [
            'conversation' => $conversation->fresh()->load(['messages.sender:id,name']),
            'unreadCount' => $this->chat->totalAdminUnread(),
        ]);
    }

    public function store(SupportMessageRequest $request, SupportConversation $conversation): JsonResponse
    {
        if (! $conversation->isOpen()) {
            return response()->json(['message' => 'Conversation is closed.'], 422);
        }

        $message = $this->chat->sendAdminMessage(
            $conversation,
            $request->user(),
            $request->validated('body'),
            $request->file('attachment')
        );

        return response()->json([
            'message' => $this->chat->formatMessage($message),
        ], 201);
    }

    public function poll(Request $request, SupportConversation $conversation): JsonResponse
    {
        $sinceId = $request->integer('since') ?: null;
        $messages = $this->chat->messagesSince($conversation, $sinceId);

        if ($request->boolean('mark_read', ! $sinceId)) {
            $this->chat->markReadByAdmin($conversation);
        }

        return response()->json([
            'messages' => $messages,
            'conversation' => [
                'uuid' => $conversation->uuid,
                'status' => $conversation->status,
                'admin_unread_count' => $conversation->fresh()->admin_unread_count,
            ],
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        return response()->json(
            $this->chat->adminNotifications($request->integer('since_message_id') ?: null)
        );
    }

    public function close(SupportConversation $conversation): RedirectResponse
    {
        $this->chat->closeConversation($conversation);

        return redirect()->route('admin.support.index')->with('success', 'Conversation closed.');
    }
}
