<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Services\SeoService;
use Illuminate\View\View;

class BlogController extends Controller
{
    use RendersThemeViews;

    public function __construct(
        private PostRepositoryInterface $posts,
        private SeoService $seo
    ) {}

    public function index(): View
    {
        if (auth()->check() && ! auth()->user()->canAccessBlog()) {
            abort(403, 'You are not allowed to access the blog.');
        }

        return $this->themeView('blog.index', [
            'posts' => $this->posts->paginatePublished(12),
            'seo' => $this->seo->meta(['title' => 'Blog | '.setting('store', 'name')]),
        ]);
    }

    public function show(string $slug): View
    {
        if (auth()->check() && ! auth()->user()->canAccessBlog()) {
            abort(403, 'You are not allowed to access the blog.');
        }

        $post = $this->posts->findBySlug($slug);

        if (! $post) {
            abort(404);
        }

        $canPreview = request()->boolean('preview')
            && auth()->check()
            && auth()->user()->role->canManageSettings();

        if ($post->status !== ContentStatus::Published && ! $canPreview) {
            abort(404);
        }

        return $this->themeView('blog.show', [
            'post' => $post,
            'relatedPosts' => $this->posts->getRelated($post, 4),
            'seo' => $this->seo->meta([
                'title' => $post->meta_title ?: $post->title,
                'description' => $post->meta_description ?: $post->excerpt,
                'image' => image_url($post->og_image ?: $post->featured_image),
            ]),
        ]);
    }
}
