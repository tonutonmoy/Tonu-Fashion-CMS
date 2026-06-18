<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MongoExists implements ValidationRule
{
    public function __construct(
        private string $modelClass,
        private string $column = 'id'
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $exists = $this->modelClass::query()->where($this->column, $value)->exists();

        if (! $exists) {
            $fail(__('validation.exists'));
        }
    }
}
