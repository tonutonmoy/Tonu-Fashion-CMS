@php
    $isActive = fn (array $patterns) => collect($patterns)->contains(fn ($p) => request()->routeIs($p));
    $linkClass = fn (bool $active) => 'builder-nav-link'.($active ? ' is-active' : '');
@endphp

<div class="builder-nav builder-nav-compact">
    <div class="flex items-center justify-between gap-2 mb-3 flex-wrap">
        <div class="flex items-center gap-2 min-w-0">
            <button type="button" class="text-xs text-gray-500 hover:text-gray-800 flex items-center gap-1 shrink-0" data-builder-nav-toggle aria-expanded="true">
                <svg class="w-4 h-4 transition" data-builder-nav-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                <span>Menu</span>
            </button>
            <button type="button" class="text-xs text-gray-500 hover:text-gray-800 shrink-0 lg:hidden" data-admin-sidebar-open>☰ Admin</button>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($hasUnpublishedChanges ?? false)
                <span class="builder-draft-badge" title="Changes are visible in Live Preview only">Unpublished draft</span>
            @endif
            <form
                action="{{ route('admin.builder.publish') }}"
                method="POST"
                class="inline"
                data-confirm
                data-confirm-title="Publish to live site?"
                data-confirm-message="All draft builder changes will go live on your storefront."
                data-confirm-ok="Publish"
            >
                @csrf
                <button type="submit" class="builder-publish-btn {{ ($hasUnpublishedChanges ?? false) ? 'has-changes' : '' }}">
                    Publish
                </button>
            </form>
        </div>
    </div>

    <div class="builder-nav-body" data-builder-nav-body>
        <div class="builder-nav-scroll">
            <a href="{{ route('admin.builder.index') }}" class="{{ $linkClass($isActive(['admin.builder.index'])) }}">Overview</a>
            <span class="builder-nav-sep">|</span>
            <a href="{{ route('admin.theme.customizer') }}" class="{{ $linkClass($isActive(['admin.theme.customizer'])) }}">Theme</a>
            <a href="{{ route('admin.theme.homepage') }}" class="{{ $linkClass($isActive(['admin.theme.homepage*'])) }}">Homepage</a>
            <a href="{{ route('admin.theme.hero-slides') }}" class="{{ $linkClass($isActive(['admin.theme.hero-slides*'])) }}">Hero</a>
            <a href="{{ route('admin.theme.footer') }}" class="{{ $linkClass($isActive(['admin.theme.footer'])) }}">Footer</a>
            <a href="{{ route('admin.theme.seo') }}" class="{{ $linkClass($isActive(['admin.theme.seo'])) }}">SEO</a>
            <span class="builder-nav-sep">|</span>
            <a href="{{ route('admin.cms.menus.index') }}" class="{{ $linkClass($isActive(['admin.cms.menus.*'])) }}">Menus</a>
            <a href="{{ route('admin.cms.pages.index') }}" class="{{ $linkClass($isActive(['admin.cms.pages.*'])) }}">Pages</a>
            <a href="{{ route('admin.cms.blog.index') }}" class="{{ $linkClass($isActive(['admin.cms.blog.*'])) }}">Blog</a>
            <a href="{{ route('admin.cms.media.index') }}" class="{{ $linkClass($isActive(['admin.cms.media.*'])) }}">Media</a>
        </div>
        <p class="text-xs text-gray-500 mt-2">Save edits as draft — they appear in Live Preview only until you click <strong>Publish</strong>.</p>
    </div>
</div>
