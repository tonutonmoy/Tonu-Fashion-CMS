@extends(theme_layout())

@section('content')
<div class="theme-container py-8 sm:py-12">
    @if($page->banner_image)
        <img src="{{ image_url($page->banner_image) }}" alt="{{ trans_field($page, 'title') }}" class="w-full h-48 sm:h-64 object-cover rounded-2xl mb-8" loading="eager">
    @endif
    <h1 class="theme-page-title mb-6">{{ trans_field($page, 'title') }}</h1>
    <div class="prose max-w-none cms-content">{!! trans_field($page, 'content') !!}</div>
</div>
@endsection
