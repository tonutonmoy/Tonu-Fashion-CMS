@extends(theme_layout())

@section('content')
<div class="theme-container py-8">
    <article class="max-w-3xl mx-auto">
        @if($post->featured_image)
            <img src="{{ image_url($post->featured_image) }}" alt="{{ trans_field($post, 'title') }}" class="w-full rounded-2xl mb-8 aspect-video object-cover" loading="eager">
        @endif
        <p class="text-sm text-gray-500 mb-2">{{ $post->published_at?->format('M d, Y') }} · {{ $post->category?->name }}</p>
        <h1 class="theme-page-title mb-6">{{ trans_field($post, 'title') }}</h1>
        <div class="prose max-w-none cms-content">{!! trans_field($post, 'content') !!}</div>
    </article>

    @if($relatedPosts->isNotEmpty())
    <section class="mt-16">
        <h2 class="text-xl font-semibold mb-6">Related Posts</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($relatedPosts as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="card p-4 block hover:shadow-md transition">
                <h3 class="font-medium text-sm">{{ trans_field($related, 'title') }}</h3>
            </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
