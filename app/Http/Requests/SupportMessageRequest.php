<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'image', 'max:4096'],
            'guest_token' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('body') && ! $this->hasFile('attachment')) {
                $validator->errors()->add('body', 'Message or image is required.');
            }
        });
    }
}
