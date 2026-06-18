<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MarketingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'facebook_pixel_id' => ['nullable', 'string', 'max:100'],
            'facebook_access_token' => ['nullable', 'string', 'max:500'],
            'facebook_dataset_id' => ['nullable', 'string', 'max:100'],
            'test_event_code' => ['nullable', 'string', 'max:100'],
            'ga_measurement_id' => ['nullable', 'string', 'max:100'],
            'gtm_id' => ['nullable', 'string', 'max:100'],
            'tiktok_pixel_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}
