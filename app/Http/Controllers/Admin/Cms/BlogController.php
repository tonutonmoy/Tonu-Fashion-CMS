<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Services\BlogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private PostRepositoryInterface $posts,
        private BlogService $blog
    ) {}

    public function index(Request $request): View
    {
        return view('admin.cms.blog.index', [
            'posts' => $this->posts->paginateAdmin($request->all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.cms.blog.create', [
            'categories' => BlogCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $this->blog->create(
            $request->validated(),
            $request->file('featured_image'),
            $request->input('tags', [])
        );

        return redirect()->route('admin.cms.blog.index')->with('success', 'Post created.');
    }

    public function edit(Post $post): View
    {
        $post->load('tags');

        return view('admin.cms.blog.edit', [
            'post' => $post,
            'categories' => BlogCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->blog->update(
            $post->id,
            $request->validated(),
            $request->file('featured_image'),
            $request->input('tags', [])
        );

        return redirect()->route('admin.cms.blog.index')->with('success', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->blog->delete($post->id);

        return back()->with('success', 'Post deleted.');
    }
}
