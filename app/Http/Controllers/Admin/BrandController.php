<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(
        private BrandRepositoryInterface $brands,
        private ImageService $images
    ) {}

    public function index(Request $request): View
    {
        return view('admin.brands.index', [
            'brands' => $this->brands->paginateAdmin($request->all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(BrandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->images->upload($request->file('logo'), 'brands', 400);
        }

        $this->brands->create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', ['brand' => $brand]);
    }

    public function update(BrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $this->images->delete($brand->logo);
            $data['logo'] = $this->images->upload($request->file('logo'), 'brands', 400);
        }

        $data['slug'] = $this->uniqueSlug(
            $data['slug'] ?? $data['name'],
            $brand->id
        );

        $this->brands->update($brand->id, $data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->images->delete($brand->logo);
        $this->brands->delete($brand->id);

        return back()->with('success', 'Brand deleted.');
    }

    private function uniqueSlug(string $value, ?int $exceptId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (Brand::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }
}
