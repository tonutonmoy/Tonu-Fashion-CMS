<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^01[0-9]{9}$/'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'delivery_zone' => ['nullable', 'in:inside_dhaka,outside_dhaka'],
            'payment_method' => ['nullable', 'string', 'in:cash_on_delivery,bkash,nagad,sslcommerz'],
            'purchase_event_id' => ['nullable', 'string', 'max:100'],
            'fbp' => ['nullable', 'string', 'max:255'],
            'fbc' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Enter a valid Bangladesh mobile number (01XXXXXXXXX).',
        ];
    }
}
