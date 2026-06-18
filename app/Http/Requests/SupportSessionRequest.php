<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_phone' => ['required', 'string', 'regex:/^01[0-9]{9}$/'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_token' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'guest_phone.regex' => 'Enter a valid Bangladesh mobile number (01XXXXXXXXX).',
        ];
    }
}
