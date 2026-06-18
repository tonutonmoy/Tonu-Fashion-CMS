<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialChatSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'messenger_enabled' => ['nullable', 'boolean'],
            'messenger_link' => ['nullable', 'url', 'max:500'],
            'instagram_enabled' => ['nullable', 'boolean'],
            'instagram_link' => ['nullable', 'url', 'max:500'],
            'telegram_enabled' => ['nullable', 'boolean'],
            'telegram_link' => ['nullable', 'url', 'max:500'],
            'support_chat_enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['whatsapp', 'messenger', 'instagram', 'telegram', 'support_chat'] as $channel) {
            $this->merge(["{$channel}_enabled" => $this->boolean("{$channel}_enabled")]);
        }
    }
}
