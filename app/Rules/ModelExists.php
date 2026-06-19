<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ModelExists implements ValidationRule
{
    public function __construct(private string $modelClass) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! $this->modelClass::query()->whereKey($value)->exists()) {
            $fail(__('validation.exists'));
        }
    }
}
