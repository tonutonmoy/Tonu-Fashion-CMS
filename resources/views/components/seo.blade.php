@props(['meta' => []])

<title>{{ $meta['title'] ?? config('app.name') }}</title>
<meta name="description" content="{{ $meta['description'] ?? '' }}">
<link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
<meta property="og:title" content="{{ $meta['title'] ?? config('app.name') }}">
<meta property="og:description" content="{{ $meta['description'] ?? '' }}">
<meta property="og:type" content="{{ $meta['og_type'] ?? 'website' }}">
<meta property="og:url" content="{{ $meta['canonical'] ?? url()->current() }}">
@if(!empty($meta['og_image']))
<meta property="og:image" content="{{ $meta['og_image'] }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['title'] ?? config('app.name') }}">
<meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">
@if(!empty($meta['og_image']))
<meta name="twitter:image" content="{{ $meta['og_image'] }}">
@endif
@if(!empty($meta['twitter_handle']))
<meta name="twitter:site" content="{{ $meta['twitter_handle'] }}">
@endif
@if(!empty($meta['json_ld']))
    @php $schemas = is_array($meta['json_ld']) && isset($meta['json_ld']['@context']) ? [$meta['json_ld']] : (array) $meta['json_ld']; @endphp
    @foreach($schemas as $schema)
        @if(is_array($schema))
        <script type="application/ld+json">{!! json_encode(array_filter($schema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
    @endforeach
@endif
