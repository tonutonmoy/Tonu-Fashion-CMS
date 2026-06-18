<div class="mb-6 flex flex-wrap gap-4 border-b border-gray-200 pb-4 text-sm">
    <a href="{{ route('admin.marketing.index') }}" class="{{ request()->routeIs('admin.marketing.index') ? 'font-semibold text-brand-600' : 'text-gray-600' }}">Pixels & CAPI</a>
    <a href="{{ route('admin.marketing.shipping') }}" class="{{ request()->routeIs('admin.marketing.shipping') ? 'font-semibold text-brand-600' : 'text-gray-600' }}">Shipping (BD)</a>
    <a href="{{ route('admin.marketing.sms') }}" class="{{ request()->routeIs('admin.marketing.sms') ? 'font-semibold text-brand-600' : 'text-gray-600' }}">SMS</a>
    <a href="{{ route('admin.marketing.social-chat') }}" class="{{ request()->routeIs('admin.marketing.social-chat') ? 'font-semibold text-brand-600' : 'text-gray-600' }}">Social Chat</a>
    <a href="{{ route('admin.marketing.seo') }}" class="{{ request()->routeIs('admin.marketing.seo') ? 'font-semibold text-brand-600' : 'text-gray-600' }}">SEO</a>
</div>
