<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageOrders() ?? false;
    }

    public function rules(): array
    {
        $divisions = array_keys(config('bangladesh.divisions', []));

        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^01[0-9]{9}$/'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'shipping_division' => ['required', 'string', Rule::in($divisions)],
            'shipping_district' => ['required', 'string', 'max:100'],
            'shipping_area' => ['nullable', 'string', 'max:100'],
            'shipping_upazila' => ['nullable', 'string', 'max:100'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'order_note' => ['nullable', 'string', 'max:1000'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'string', 'in:cash_on_delivery,bkash,nagad,sslcommerz'],
            'status' => ['required', 'string', Rule::in(array_column(OrderStatus::cases(), 'value'))],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
