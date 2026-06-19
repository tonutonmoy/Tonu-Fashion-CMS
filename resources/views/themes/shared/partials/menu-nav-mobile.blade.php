@props(['items' => collect()])

@if($items->isNotEmpty())
    @foreach($items as $item)
        @if($item->children->isNotEmpty())
            <div class="mobile-nav-group" data-mobile-nav-group>
                <button
                    type="button"
                    class="mobile-nav-link mobile-nav-toggle w-full flex items-center justify-between gap-2 text-left"
                    data-mobile-submenu-toggle
                    aria-expanded="false"
                >
                    <span>{{ $item->title }}</span>
                    <svg class="mobile-nav-chevron w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="mobile-nav-submenu hidden" data-mobile-submenu>
                    @foreach($item->children as $child)
                        <a
                            href="{{ $child->resolvedUrl() }}"
                            class="mobile-nav-link mobile-nav-sublink"
                            @if($child->open_in_new_tab) target="_blank" rel="noopener" @endif
                        >{{ $child->title }}</a>
                    @endforeach
                </div>
            </div>
        @else
            <a
                href="{{ $item->resolvedUrl() }}"
                class="mobile-nav-link"
                @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif
            >{{ $item->title }}</a>
        @endif
    @endforeach
@endif
