@foreach($sectionKeys ?? [] as $sectionKey)
    @php
        $view = config("cms.homepage_sections.{$sectionKey}");
        $productKeys = config('cms.product_section_keys', []);
    @endphp
    @if($view && view()->exists($view))
        <div id="section-{{ $sectionKey }}">
        @if(in_array($sectionKey, $productKeys, true))
            @include($view, ['sections' => $sections, 'sectionKey' => $sectionKey])
        @else
            @include($view, ['sections' => $sections])
        @endif
        </div>
    @endif
@endforeach
