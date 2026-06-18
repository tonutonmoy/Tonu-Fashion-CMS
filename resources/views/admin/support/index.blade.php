@extends('layouts.admin')

@section('title', 'Support Chat')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Support Chat</h1>
        <p class="text-sm text-gray-500 mt-1">Real-time customer conversations</p>
    </div>
    <div class="flex items-center gap-2">
        <span id="support-admin-unread" class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-700 {{ $unreadCount ? '' : 'hidden' }}">{{ $unreadCount }} unread</span>
        <a href="{{ route('admin.support.index', ['status' => 'open']) }}" class="btn-secondary text-sm {{ request('status', 'open') === 'open' ? 'ring-2 ring-red-500' : '' }}">Open</a>
        <a href="{{ route('admin.support.index', ['status' => 'closed']) }}" class="btn-secondary text-sm {{ request('status') === 'closed' ? 'ring-2 ring-red-500' : '' }}">Closed</a>
    </div>
</div>

<div class="card overflow-hidden" id="support-inbox" data-poll-url="{{ route('admin.support.notifications') }}">
    <div class="divide-y divide-gray-100">
        @forelse($conversations as $conversation)
        @php $latest = $conversation->messages->first(); @endphp
        <a href="{{ route('admin.support.show', $conversation) }}" class="flex items-start gap-4 p-4 hover:bg-gray-50 transition support-inbox-item" data-conversation="{{ $conversation->uuid }}">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-900 text-white font-semibold text-sm">
                {{ strtoupper(substr($conversation->guest_name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="font-medium text-gray-900 truncate">{{ $conversation->guest_name }}</p>
                    <span class="text-xs text-gray-500 shrink-0">{{ $conversation->last_message_at?->diffForHumans() ?? $conversation->created_at->diffForHumans() }}</span>
                </div>
                @if($conversation->guest_email)
                <p class="text-xs text-gray-500">{{ $conversation->guest_email }}</p>
                @endif
                @if($conversation->guest_phone)
                <p class="text-sm font-medium text-gray-700">{{ $conversation->guest_phone }}</p>
                @endif
                <p class="text-sm text-gray-600 truncate mt-1">{{ $latest?->body ?: ($latest?->attachment ? '[Image]' : 'No messages yet') }}</p>
            </div>
            @if($conversation->admin_unread_count > 0)
            <span class="support-unread-badge shrink-0 min-w-[1.5rem] h-6 px-2 rounded-full bg-red-600 text-white text-xs font-semibold flex items-center justify-center">{{ $conversation->admin_unread_count }}</span>
            @endif
        </a>
        @empty
        <div class="p-12 text-center text-gray-500">No conversations yet.</div>
        @endforelse
    </div>
</div>

<div class="mt-4">{{ $conversations->withQueryString()->links() }}</div>
@endsection
