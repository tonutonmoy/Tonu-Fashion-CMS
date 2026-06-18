<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdminPermission;
use App\Enums\RecordStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageUsers() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $role = UserRole::tryFrom((string) $this->input('role')) ?? UserRole::Staff;

        $this->merge([
            'permissions' => AdminPermission::normalizeFromInput(
                (array) $this->input('permissions', []),
                $role
            ),
        ]);
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_map(fn (UserRole $role) => $role->value, UserRole::assignableTeamRoles()))],
            'status' => ['required', Rule::in(array_column(RecordStatus::cases(), 'value'))],
            'permissions' => ['required', 'array'],
            'permissions.store' => ['boolean'],
            'permissions.blog' => ['boolean'],
            'permissions.cms' => ['boolean'],
            'permissions.settings' => ['boolean'],
        ];
    }
}
