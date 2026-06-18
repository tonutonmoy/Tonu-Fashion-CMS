@foreach($sectionKeys ?? [] as $sectionKey)
    @php
        $view = config("cms.homepage_sections.{$sectionKey}");
        $productKeys = config('cms.product_section_keys', []);
        $isLazy = in_array($sectionKey, $lazySectionKeys ?? [], true);
    @endphp
    @if($isLazy)
        <div
            id="section-{{ $sectionKey }}"
            class="theme-lazy-section"
            data-lazy-section="{{ $sectionKey }}"
            data-lazy-url="{{ route('home.section', $sectionKey) }}"
        >
            <div class="theme-container py-10">
                <div class="theme-lazy-skeleton animate-pulse space-y-4">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-48"></div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @for($i = 0; $i < 4; $i++)
                            <div class="aspect-[3/4] bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    @elseif($view && view()->exists($view))
        <div id="section-{{ $sectionKey }}">
            @if(in_array($sectionKey, $productKeys, true))
                @include($view, ['sections' => $sections, 'sectionKey' => $sectionKey])
            @else
                @include($view, ['sections' => $sections])
            @endif
        </div>
    @endif
@endforeach
