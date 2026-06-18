@php
    $permissionMap = $permissionMap ?? ($member->adminPermissionsMap() ?? \App\Enums\AdminPermission::defaultMapForRole(\App\Enums\UserRole::Staff));
@endphp
<div class="border border-gray-200 rounded-xl p-4 space-y-3">
    <div>
        <h3 class="font-semibold text-sm">Permissions</h3>
        <p class="text-xs text-gray-500 mt-1">Choose what this team member can manage in the admin panel.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach(\App\Enums\AdminPermission::cases() as $permission)
        @php
            $checked = (bool) old('permissions.'.$permission->value, $permissionMap[$permission->value] ?? false);
        @endphp
        <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:border-gray-300 cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/30">
            <input
                type="checkbox"
                name="permissions[{{ $permission->value }}]"
                value="1"
                class="mt-1 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                data-team-permission="{{ $permission->value }}"
                @checked($checked)
            >
            <span>
                <span class="block text-sm font-medium text-gray-900">{{ $permission->label() }}</span>
                <span class="block text-xs text-gray-500 mt-0.5">{{ $permission->description() }}</span>
            </span>
        </label>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const roleDefaults = @json(collect(\App\Enums\UserRole::assignableTeamRoles())->mapWithKeys(
        fn ($role) => [$role->value => \App\Enums\AdminPermission::defaultMapForRole($role)]
    ));
    const roleSelect = document.querySelector('[data-team-role-select]');
    if (!roleSelect) return;

    roleSelect.addEventListener('change', () => {
        const defaults = roleDefaults[roleSelect.value] || {};
        document.querySelectorAll('[data-team-permission]').forEach((input) => {
            input.checked = !!defaults[input.dataset.teamPermission];
        });
    });
});
</script>
