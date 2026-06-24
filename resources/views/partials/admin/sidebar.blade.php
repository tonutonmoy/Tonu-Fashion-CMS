<aside id="admin-sidebar" class="admin-sidebar w-64 bg-gray-900 text-gray-300 flex-shrink-0 fixed lg:static inset-y-0 left-0 z-[70] -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="p-6 border-b border-gray-800 flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-lg flex items-center gap-2">
            <x-admin.icon name="dashboard" class="w-6 h-6 text-red-400" />
            Admin Panel
        </a>
        <button type="button" id="admin-sidebar-close" class="lg:hidden text-gray-400 hover:text-white p-1" aria-label="Close sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="p-4 space-y-1 text-sm max-h-[calc(100vh-5rem)] overflow-y-auto">
        @php
            $user = auth()->user();
            $nav = [
                ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'visible' => true],
                ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'customers', 'label' => 'Team Members', 'visible' => $user?->role->canManageUsers()],
                ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'products', 'label' => 'Products', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.inventory.index', 'match' => 'admin.inventory.*', 'icon' => 'products', 'label' => 'Inventory', 'visible' => $user?->canAdmin('store')],
                ['type' => 'group', 'label' => 'Reports', 'icon' => 'chart', 'visible' => $user?->canAdmin('store'), 'match' => 'admin.reports.*', 'children' => [
                    ['route' => 'admin.reports.profit-loss', 'match' => 'admin.reports.profit-loss', 'label' => 'Profit & Loss'],
                    ['route' => 'admin.reports.inventory-details', 'match' => 'admin.reports.inventory-details', 'label' => 'Inventory Details'],
                ]],
                ['route' => 'admin.expenses.index', 'match' => 'admin.expenses.*', 'icon' => 'revenue', 'label' => 'Expenses', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'categories', 'label' => 'Categories', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.brands.index', 'match' => 'admin.brands.*', 'icon' => 'brands', 'label' => 'Brands', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'icon' => 'orders', 'label' => 'Orders', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.support.index', 'match' => 'admin.support.*', 'icon' => 'support', 'label' => 'Support Chat', 'visible' => $user?->canAdmin('store'), 'badge' => true],
                ['route' => 'admin.cms.blog.index', 'match' => 'admin.cms.blog.*', 'icon' => 'blog', 'label' => 'Blog', 'visible' => $user?->canAdmin('blog')],
                ['route' => 'admin.cms.pages.index', 'match' => 'admin.cms.pages.*,admin.cms.menus.*,admin.cms.media.*', 'icon' => 'pages', 'label' => 'Pages & Media', 'visible' => $user?->canAdmin('cms')],
                ['route' => 'admin.payment.index', 'match' => 'admin.payment.*', 'icon' => 'payment', 'label' => 'Payment Gateways', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.courier.index', 'match' => 'admin.courier.*', 'icon' => 'courier', 'label' => 'Courier & Automation', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.customers.index', 'match' => 'admin.customers.*', 'icon' => 'customers', 'label' => 'Customers', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.coupons.index', 'match' => 'admin.coupons.*', 'icon' => 'coupons', 'label' => 'Coupons', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.reviews.index', 'match' => 'admin.reviews.*', 'icon' => 'reviews', 'label' => 'Reviews', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.marketing.index', 'match' => 'admin.marketing.*', 'icon' => 'marketing', 'label' => 'Marketing & BD', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.builder.index', 'match' => 'admin.builder.*,admin.theme.*', 'icon' => 'builder', 'label' => 'Website Builder', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.backup.index', 'match' => 'admin.backup.*', 'icon' => 'settings', 'label' => 'Backups', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.performance.index', 'match' => 'admin.performance.*', 'icon' => 'dashboard', 'label' => 'Performance', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.settings.store', 'match' => 'admin.settings.*', 'icon' => 'settings', 'label' => 'Settings', 'visible' => $user?->canAdmin('settings')],
            ];
        @endphp
        @foreach($nav as $item)
        @if($item['visible'])
        @if(($item['type'] ?? '') === 'group')
        @php
            $patterns = explode(',', $item['match']);
            $groupActive = collect($patterns)->contains(fn ($p) => request()->routeIs(trim($p)));
        @endphp
        <div class="admin-nav-group" data-admin-nav-group>
            <button type="button" data-admin-nav-toggle class="admin-nav-link w-full {{ $groupActive ? 'is-active' : '' }}" aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                <x-admin.icon :name="$item['icon']" class="w-5 h-5 shrink-0 {{ $groupActive ? 'text-red-400' : 'text-gray-400' }}" />
                <span class="flex-1 text-left">{{ $item['label'] }}</span>
                <svg class="w-4 h-4 shrink-0 transition-transform admin-nav-chevron {{ $groupActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ml-4 mt-1 space-y-1 {{ $groupActive ? '' : 'hidden' }}" data-admin-nav-children>
                @foreach($item['children'] as $child)
                @php $childActive = request()->routeIs($child['match']); @endphp
                <a href="{{ route($child['route']) }}" class="block px-3 py-2 rounded-lg text-sm {{ $childActive ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    {{ $child['label'] }}
                </a>
                @endforeach
            </div>
        </div>
        @else
        @php
            $patterns = explode(',', $item['match']);
            $active = collect($patterns)->contains(fn ($p) => request()->routeIs(trim($p)));
        @endphp
        <a href="{{ route($item['route']) }}" class="admin-nav-link {{ $active ? 'is-active' : '' }}">
            <x-admin.icon :name="$item['icon']" class="w-5 h-5 shrink-0 {{ $active ? 'text-red-400' : 'text-gray-400' }}" />
            <span class="flex-1">{{ $item['label'] }}</span>
            @if(!empty($item['badge']))
            <span id="admin-support-nav-badge" class="hidden min-w-[1.25rem] h-5 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">0</span>
            @endif
        </a>
        @endif
        @endif
        @endforeach
    </nav>
</aside>
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/40 z-[60] hidden lg:hidden" aria-hidden="true"></div>
