@extends('layouts.admin')
@section('title', 'Social Chat Widget')
@section('content')
@include('admin.marketing._nav')
<form action="{{ route('admin.marketing.social-chat') }}" method="POST" class="max-w-xl card p-6 space-y-4">
    @csrf @method('PUT')
    <div class="flex items-center gap-3"><input type="checkbox" name="whatsapp_enabled" value="1" @checked($settings['whatsapp_enabled'] ?? false)><label>WhatsApp</label></div>
    <input name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" class="input" placeholder="88017XXXXXXXX">
    <div class="flex items-center gap-3"><input type="checkbox" name="messenger_enabled" value="1" @checked($settings['messenger_enabled'] ?? false)><label>Messenger</label></div>
    <input name="messenger_link" value="{{ $settings['messenger_link'] ?? '' }}" class="input" placeholder="https://m.me/yourpage">
    <div class="flex items-center gap-3"><input type="checkbox" name="instagram_enabled" value="1" @checked($settings['instagram_enabled'] ?? false)><label>Instagram</label></div>
    <input name="instagram_link" value="{{ $settings['instagram_link'] ?? '' }}" class="input">
    <div class="flex items-center gap-3"><input type="checkbox" name="telegram_enabled" value="1" @checked($settings['telegram_enabled'] ?? false)><label>Telegram</label></div>
    <input name="telegram_link" value="{{ $settings['telegram_link'] ?? '' }}" class="input" placeholder="https://t.me/yourchannel">
    <hr class="border-gray-200">
    <h3 class="font-semibold">Live Support Chat</h3>
    <div class="flex items-center gap-3">
        <input type="hidden" name="support_chat_enabled" value="0">
        <input type="checkbox" name="support_chat_enabled" value="1" @checked(filter_var($settings['support_chat_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN))>
        <label>Enable on-site support chat widget</label>
    </div>
    <button class="btn-primary">Save Social Chat</button>
</form>
@endsection
