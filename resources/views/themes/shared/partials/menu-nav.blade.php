@props(['items' => collect(), 'class' => ''])

@if($items->isNotEmpty())
    @foreach($items as $item)
        @if($item->children->isNotEmpty())
            <div class="relative group {{ $class }}">
                <button type="button" class="theme-nav-dropdown flex items-center gap-1">
                    {{ $item->title }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="hidden group-hover:block absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 min-w-[12rem] z-50">
                    @foreach($item->children as $child)
                        <a href="{{ $child->resolvedUrl() }}" class="block px-4 py-2 text-sm hover:bg-gray-50" @if($child->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $child->title }}</a>
                    @endforeach
                </div>
            </div>
        @else
            <a href="{{ $item->resolvedUrl() }}" class="{{ $class }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->title }}</a>
        @endif
    @endforeach
@endif
