@if(isset($sections['blog']) && $sections['blog']['posts']->isNotEmpty())
<section class="theme-section">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">From Our Blog</h2>
            <a href="{{ route('blog.index') }}" class="theme-link">View all</a>
        </div>
        <div class="theme-blog-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($sections['blog']['posts'] as $post)
            <article class="theme-blog-card card overflow-hidden">
                @if($post->featured_image)
                    <a href="{{ route('blog.show', $post->slug) }}">
                        <img src="{{ image_url($post->featured_image) }}" alt="{{ trans_field($post, 'title') }}" loading="lazy" class="w-full h-48 object-cover">
                    </a>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold"><a href="{{ route('blog.show', $post->slug) }}" class="hover:text-brand-600">{{ trans_field($post, 'title') }}</a></h3>
                    <p class="text-sm text-gray-500 mt-2 line-clamp-3">{{ trans_field($post, 'excerpt') }}</p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif
