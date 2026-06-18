@extends(theme_layout())

@section('content')
<div class="theme-container py-8">
    <h1 class="theme-page-title mb-8">Blog</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <article class="card overflow-hidden">
            @if($post->featured_image)
                <a href="{{ route('blog.show', $post->slug) }}"><img src="{{ image_url($post->featured_image) }}" alt="" class="w-full h-48 object-cover" loading="lazy"></a>
            @endif
            <div class="p-4">
                <h2 class="font-semibold text-lg"><a href="{{ route('blog.show', $post->slug) }}">{{ trans_field($post, 'title') }}</a></h2>
                <p class="text-sm text-gray-500 mt-2">{{ trans_field($post, 'excerpt') }}</p>
            </div>
        </article>
        @endforeach
    </div>
    <div class="mt-8">{{ $posts->links() }}</div>
</div>
@endsection
