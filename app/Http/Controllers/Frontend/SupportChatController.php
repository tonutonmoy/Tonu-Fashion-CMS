<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportMessageRequest;
use App\Http\Requests\SupportSessionRequest;
use App\Models\SupportConversation;
use App\Services\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function __construct(private SupportChatService $chat) {}

    public function resume(Request $request): JsonResponse
    {
        if (! $this->chat->isEnabled()) {
            return response()->json(['message' => 'Support chat is disabled.'], 403);
        }

        $token = $this->chat->syncGuestToken($request->input('guest_token'));
        $phone = $request->input('guest_phone');

        $conversation = $this->chat->resumeConversation($token, $phone);

        if (! $conversation) {
            return response()->json([
                'guest_token' => $token,
                'conversation' => null,
                'messages' => [],
            ]);
        }

        $conversation = $this->chat->attachGuestToken($conversation, $token);

        return response()->json([
            'guest_token' => $token,
            'conversation' => [
                'uuid' => $conversation->uuid,
                'guest_name' => $conversation->guest_name,
                'guest_phone' => $conversation->guest_phone,
                'status' => $conversation->status,
            ],
            'messages' => $this->chat->messagesSince($conversation),
        ]);
    }

    public function session(SupportSessionRequest $request): JsonResponse
    {
        if (! $this->chat->isEnabled()) {
            return response()->json(['message' => 'Support chat is disabled.'], 403);
        }

        $token = $this->chat->syncGuestToken($request->input('guest_token'));

        $conversation = $this->chat->findOrCreateConversation(
            $token,
            $request->validated('guest_name'),
            $request->validated('guest_phone'),
            $request->validated('guest_email')
        );

        $this->chat->markReadByCustomer($conversation);

        return response()->json([
            'guest_token' => $token,
            'conversation' => [
                'uuid' => $conversation->uuid,
                'guest_name' => $conversation->guest_name,
                'guest_phone' => $conversation->guest_phone,
                'status' => $conversation->status,
            ],
            'messages' => $this->chat->messagesSince($conversation),
        ]);
    }

    public function show(Request $request, SupportConversation $conversation): JsonResponse
    {
        $token = $this->chat->syncGuestToken($request->input('guest_token'));
        $phone = $request->input('guest_phone');

        if (! $this->chat->authorizeCustomerAccess($conversation, $token, $phone)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation = $this->chat->attachGuestToken($conversation, $token);

        $sinceId = $request->has('since') ? $request->integer('since') : null;
        $messages = $this->chat->messagesSince($conversation, $sinceId ?: null);

        if (! $request->has('since')) {
            $this->chat->markReadByCustomer($conversation);
        }

        return response()->json([
            'conversation' => [
                'uuid' => $conversation->uuid,
                'guest_name' => $conversation->guest_name,
                'guest_phone' => $conversation->guest_phone,
                'status' => $conversation->status,
                'customer_unread_count' => $conversation->fresh()->customer_unread_count,
            ],
            'messages' => $messages,
        ]);
    }

    public function store(SupportMessageRequest $request, SupportConversation $conversation): JsonResponse
    {
        $token = $this->chat->syncGuestToken($request->input('guest_token'));
        $phone = $request->input('guest_phone');

        if (! $this->chat->authorizeCustomerAccess($conversation, $token, $phone)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation = $this->chat->attachGuestToken($conversation, $token);

        if (! $conversation->isOpen()) {
            return response()->json(['message' => 'This conversation is closed.'], 422);
        }

        $message = $this->chat->sendCustomerMessage(
            $conversation,
            $request->validated('body'),
            $request->file('attachment')
        );

        return response()->json([
            'message' => $this->chat->formatMessage($message),
        ], 201);
    }

    public function read(Request $request, SupportConversation $conversation): JsonResponse
    {
        $token = $this->chat->syncGuestToken($request->input('guest_token'));
        $phone = $request->input('guest_phone');

        if (! $this->chat->authorizeCustomerAccess($conversation, $token, $phone)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation = $this->chat->attachGuestToken($conversation, $token);

        $this->chat->markReadByCustomer($conversation);

        return response()->json(['ok' => true]);
    }
}
