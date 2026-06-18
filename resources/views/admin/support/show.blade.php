@extends('layouts.admin')

@section('title', 'Chat · '.$conversation->guest_name)

@section('content')
<div class="max-w-4xl mx-auto" id="admin-support-chat"
    data-conversation="{{ $conversation->uuid }}"
    data-poll-url="{{ route('admin.support.poll', $conversation) }}"
    data-send-url="{{ route('admin.support.messages.store', $conversation) }}">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.support.index') }}" class="text-gray-500 hover:text-gray-800 shrink-0">← Back</a>
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-gray-900 truncate">{{ $conversation->guest_name }}</h1>
                <p class="text-sm text-gray-500">{{ $conversation->guest_phone ?? '—' }}</p>
                @if($conversation->guest_email)
                <p class="text-xs text-gray-400">{{ $conversation->guest_email }}</p>
                @endif
            </div>
        </div>
        @if($conversation->isOpen())
        <form action="{{ route('admin.support.close', $conversation) }}" method="POST" onsubmit="return confirm('Close this conversation?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-secondary text-sm">Close chat</button>
        </form>
        @else
        <span class="text-sm text-gray-500">Closed</span>
        @endif
    </div>

    <div class="card flex flex-col h-[calc(100vh-14rem)] min-h-[28rem]">
        <div id="support-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            @foreach($conversation->messages as $message)
            <div class="support-msg {{ $message->sender_type->value === 'admin' ? 'support-msg--admin' : 'support-msg--customer' }}" data-id="{{ $message->id }}">
                <div class="support-msg-bubble">
                    @if($message->attachment)
                    <a href="{{ image_url($message->attachment) }}" target="_blank" rel="noopener"><img src="{{ image_url($message->attachment) }}" alt="" class="support-msg-image max-w-[200px] rounded-lg mb-1"></a>
                    @endif
                    @if($message->body)
                    <p class="support-msg-text">{{ $message->body }}</p>
                    @endif
                    <span class="support-msg-time">{{ $message->created_at->timezone(config('app.timezone'))->format('g:i A') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        @if($conversation->isOpen())
        <form id="support-chat-form" class="border-t border-gray-200 p-4 flex gap-2 bg-white items-end" data-no-loading="1">
            <input type="file" id="support-chat-image" accept="image/*" class="hidden">
            <button type="button" id="support-chat-image-btn" class="btn-secondary shrink-0 px-3" title="Attach image">📷</button>
            <textarea id="support-chat-input" rows="2" class="input flex-1 resize-none" placeholder="Type your reply… (Enter to send)" maxlength="2000"></textarea>
            <button type="submit" class="btn-primary self-end shrink-0">Send</button>
        </form>
        @else
        <div class="border-t border-gray-200 p-4 text-sm text-gray-500 bg-white">This conversation is closed.</div>
        @endif
    </div>
</div>
@endsection
