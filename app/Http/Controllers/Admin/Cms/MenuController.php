<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Enums\MenuLocation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuRequest;
use App\Models\CmsPage;
use App\Services\MenuBuilderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(private MenuBuilderService $menus) {}

    public function index(): View
    {
        return view('admin.cms.menus.index', [
            'menus' => $this->menus->getAllMenus(),
            'locations' => MenuLocation::cases(),
            'pages' => CmsPage::query()->orderBy('title')->get(['id', 'title', 'slug']),
        ]);
    }

    public function edit(MenuLocation $location): View
    {
        $menuModel = \App\Models\Menu::query()
            ->where('location', $location->value)
            ->with(['items' => fn ($q) => $q->with(['children.page', 'page'])])
            ->first();

        return view('admin.cms.menus.edit', [
            'location' => $location,
            'menu' => $menuModel,
            'pages' => CmsPage::query()->orderBy('title')->get(['id', 'title', 'slug']),
        ]);
    }

    public function update(MenuRequest $request, MenuLocation $location): RedirectResponse
    {
        $this->menus->saveMenu(
            $location,
            $request->validated('name'),
            $request->validated('items', [])
        );

        return back()->with('success', 'Menu saved.');
    }
}
