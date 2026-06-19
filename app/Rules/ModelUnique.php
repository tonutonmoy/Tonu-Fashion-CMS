<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ModelUnique implements ValidationRule
{
    public function __construct(
        private string $modelClass,
        private string $column,
        private mixed $ignoreId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query = $this->modelClass::query()->where($this->column, $value);

        if ($this->ignoreId !== null) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail(__('validation.unique'));
        }
    }
}
