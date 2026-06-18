<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SmsSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'sms_enabled' => ['nullable', 'boolean'],
            'sms_api_key' => ['nullable', 'string', 'max:500'],
            'sms_sender_id' => ['nullable', 'string', 'max:20'],
            'notify_confirmed' => ['nullable', 'boolean'],
            'notify_shipped' => ['nullable', 'boolean'],
            'notify_delivered' => ['nullable', 'boolean'],
            'notify_parcel_created' => ['nullable', 'boolean'],
            'notify_returned' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sms_enabled' => $this->boolean('sms_enabled'),
            'notify_confirmed' => $this->boolean('notify_confirmed'),
            'notify_shipped' => $this->boolean('notify_shipped'),
            'notify_delivered' => $this->boolean('notify_delivered'),
            'notify_parcel_created' => $this->boolean('notify_parcel_created'),
            'notify_returned' => $this->boolean('notify_returned'),
        ]);
    }
}
