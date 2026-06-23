<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
        private ImageService $images
    ) {}

    public function index(Request $request): View
    {
        return view('admin.categories.index', [
            'categories' => $this->categories->paginateAdmin($request->all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->upload($request->file('image'), 'categories', 800);
        }

        $this->categories->create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->images->delete($category->image);
            $data['image'] = $this->images->upload($request->file('image'), 'categories', 800);
        }

        $data['slug'] = $this->uniqueSlug(
            $data['slug'] ?? $data['name'],
            $category->id
        );

        $this->categories->update($category->id, $data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->images->delete($category->image);
        $this->categories->delete($category->id);

        return back()->with('success', 'Category deleted.');
    }

    private function uniqueSlug(string $value, ?int $exceptId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (Category::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }
}
