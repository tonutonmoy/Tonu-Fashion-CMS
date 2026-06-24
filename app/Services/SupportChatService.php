<?php

namespace App\Services;

use App\Enums\SupportMessageSender;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportChatService
{
    public function __construct(
        private SettingRepositoryInterface $settings,
        private ImageService $images,
    ) {}

    public function isEnabled(): bool
    {
        return filter_var(
            $this->settings->get('social_chat', 'support_chat_enabled', true),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function guestToken(): string
    {
        $token = session('support_guest_token');

        if (! $token) {
            $token = (string) Str::uuid();
            session(['support_guest_token' => $token]);
        }

        return $token;
    }

    public function syncGuestToken(?string $token): string
    {
        if ($token) {
            session(['support_guest_token' => $token]);

            return $token;
        }

        return $this->guestToken();
    }

    public function resumeConversation(string $guestToken, ?string $phone = null): ?SupportConversation
    {
        $conversation = SupportConversation::query()
            ->where('guest_token', $guestToken)
            ->where('status', 'open')
            ->latest('updated_at')
            ->first();

        if ($conversation) {
            return $conversation;
        }

        if ($phone) {
            return SupportConversation::query()
                ->where('guest_phone', $phone)
                ->where('status', 'open')
                ->latest('updated_at')
                ->first();
        }

        return null;
    }

    public function attachGuestToken(SupportConversation $conversation, string $guestToken): SupportConversation
    {
        if ($conversation->guest_token !== $guestToken) {
            $conversation->update(['guest_token' => $guestToken]);
        }

        return $conversation->fresh();
    }

    public function findOrCreateConversation(
        string $guestToken,
        string $name,
        string $phone,
        ?string $email = null
    ): SupportConversation {
        $userId = Auth::id();

        $conversation = SupportConversation::query()
            ->where('guest_token', $guestToken)
            ->where('status', 'open')
            ->latest('updated_at')
            ->first();

        if ($conversation) {
            $conversation->update([
                'guest_name' => $name,
                'guest_phone' => $phone,
                'guest_email' => $email,
                'user_id' => $userId ?? $conversation->user_id,
            ]);

            return $conversation->fresh();
        }

        return SupportConversation::query()->create([
            'guest_token' => $guestToken,
            'user_id' => $userId,
            'guest_name' => $name,
            'guest_phone' => $phone,
            'guest_email' => $email,
            'status' => 'open',
        ]);
    }

    public function authorizeCustomerAccess(
        SupportConversation $conversation,
        string $guestToken,
        ?string $phone = null
    ): bool {
        if ($conversation->guest_token === $guestToken) {
            return true;
        }

        if ($phone && $conversation->guest_phone === $phone) {
            return true;
        }

        return Auth::check() && $conversation->user_id === Auth::id();
    }

    public function messagesSince(SupportConversation $conversation, ?int $sinceId = null): Collection
    {
        return $conversation->messages()
            ->when($sinceId, fn ($q) => $q->where('id', '>', $sinceId))
            ->with('sender:id,name')
            ->get()
            ->map(fn (SupportMessage $message) => $this->formatMessage($message));
    }

    public function sendCustomerMessage(
        SupportConversation $conversation,
        ?string $body,
        ?UploadedFile $attachment = null
    ): SupportMessage {
        return $this->storeMessage(
            $conversation,
            SupportMessageSender::Customer,
            $body,
            null,
            $this->uploadAttachment($attachment)
        );
    }

    public function sendAdminMessage(
        SupportConversation $conversation,
        User $admin,
        ?string $body,
        ?UploadedFile $attachment = null
    ): SupportMessage {
        return $this->storeMessage(
            $conversation,
            SupportMessageSender::Admin,
            $body,
            $admin->id,
            $this->uploadAttachment($attachment)
        );
    }

    public function markReadByCustomer(SupportConversation $conversation): void
    {
        DB::transaction(function () use ($conversation) {
            SupportMessage::query()
                ->where('support_conversation_id', $conversation->id)
                ->where('sender_type', SupportMessageSender::Admin)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $conversation->update(['customer_unread_count' => 0]);
        });
    }

    public function markReadByAdmin(SupportConversation $conversation): void
    {
        DB::transaction(function () use ($conversation) {
            SupportMessage::query()
                ->where('support_conversation_id', $conversation->id)
                ->where('sender_type', SupportMessageSender::Customer)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $conversation->update(['admin_unread_count' => 0]);
        });
    }

    public function inbox(?string $status = 'open'): LengthAwarePaginator
    {
        return SupportConversation::query()
            ->with(['messages' => fn ($q) => $q->latest('id')->limit(1)])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->paginate(admin_per_page());
    }

    public function totalAdminUnread(): int
    {
        return (int) SupportConversation::query()
            ->where('status', 'open')
            ->sum('admin_unread_count');
    }

    public function adminNotifications(?int $sinceMessageId = null): array
    {
        $unread = $this->totalAdminUnread();

        $messages = SupportMessage::query()
            ->with(['conversation:id,uuid,guest_name,guest_phone', 'sender:id,name'])
            ->where('sender_type', SupportMessageSender::Customer)
            ->when($sinceMessageId, fn ($q) => $q->where('id', '>', $sinceMessageId))
            ->orderBy('id')
            ->limit(20)
            ->get()
            ->map(fn (SupportMessage $message) => [
                'id' => $message->id,
                'body' => $message->body ?: '[Image]',
                'created_at' => $message->created_at?->toIso8601String(),
                'conversation_uuid' => $message->conversation?->uuid,
                'guest_name' => $message->conversation?->guest_name,
                'guest_phone' => $message->conversation?->guest_phone,
                'url' => $message->conversation
                    ? route('admin.support.show', $message->conversation)
                    : null,
            ]);

        return [
            'unread_count' => $unread,
            'messages' => $messages,
        ];
    }

    public function closeConversation(SupportConversation $conversation): void
    {
        $conversation->update(['status' => 'closed']);
    }

    private function uploadAttachment(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        return $this->images->upload($file, 'support-chat', 1200, 85, true);
    }

    private function storeMessage(
        SupportConversation $conversation,
        SupportMessageSender $sender,
        ?string $body,
        ?int $senderUserId = null,
        ?string $attachment = null
    ): SupportMessage {
        return DB::transaction(function () use ($conversation, $sender, $body, $senderUserId, $attachment) {
            $message = SupportMessage::query()->create([
                'support_conversation_id' => $conversation->id,
                'sender_type' => $sender,
                'sender_user_id' => $senderUserId,
                'body' => $body ? trim($body) : null,
                'attachment' => $attachment,
            ]);

            $conversation->update([
                'last_message_at' => $message->created_at,
                'admin_unread_count' => $sender === SupportMessageSender::Customer
                    ? $conversation->admin_unread_count + 1
                    : $conversation->admin_unread_count,
                'customer_unread_count' => $sender === SupportMessageSender::Admin
                    ? $conversation->customer_unread_count + 1
                    : $conversation->customer_unread_count,
            ]);

            return $message->load('sender:id,name');
        });
    }

    public function formatMessage(SupportMessage $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'attachment_url' => $message->attachment ? image_url($message->attachment) : null,
            'sender_type' => $message->sender_type->value,
            'sender_name' => $message->sender_type === SupportMessageSender::Admin
                ? ($message->sender?->name ?? 'Support')
                : null,
            'created_at' => $message->created_at?->toIso8601String(),
            'time_label' => $message->created_at?->timezone(config('app.timezone'))->format('g:i A'),
        ];
    }
}
