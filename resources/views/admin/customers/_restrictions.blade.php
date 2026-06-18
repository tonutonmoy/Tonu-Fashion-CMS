@props(['customer', 'action'])

<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @method('PATCH')

    <div>
        <label class="label">Account Status</label>
        <select name="status" class="input w-full">
            <option value="active" @selected($customer->status->value === 'active')>Active (can login)</option>
            <option value="inactive" @selected($customer->status->value === 'inactive')>Blocked (cannot login)</option>
        </select>
    </div>

    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="order_blocked" value="1" class="rounded border-gray-300" @checked($customer->order_blocked)>
        Block from placing orders
    </label>

    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="blog_blocked" value="1" class="rounded border-gray-300" @checked($customer->blog_blocked)>
        Block from blog & reviews
    </label>

    <button type="submit" class="btn-primary">Save Restrictions</button>
</form>
