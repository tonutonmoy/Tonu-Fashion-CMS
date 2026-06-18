<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CmsPageRequest;
use App\Models\CmsPage;
use App\Repositories\Contracts\CmsPageRepositoryInterface;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private CmsPageRepositoryInterface $pages,
        private PageService $pageService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.cms.pages.index', [
            'pages' => $this->pages->paginateAdmin($request->all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.cms.pages.create');
    }

    public function store(CmsPageRequest $request): RedirectResponse
    {
        $this->pageService->create(
            $request->validated(),
            $request->file('banner_image'),
            $request->file('og_image')
        );

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page created.');
    }

    public function edit(CmsPage $page): View
    {
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(CmsPageRequest $request, CmsPage $page): RedirectResponse
    {
        $this->pageService->update(
            $page->id,
            $request->validated(),
            $request->file('banner_image'),
            $request->file('og_image')
        );

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(CmsPage $page): RedirectResponse
    {
        $this->pageService->delete($page->id);

        return back()->with('success', 'Page deleted.');
    }
}
