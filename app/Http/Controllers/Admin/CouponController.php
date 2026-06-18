<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function __construct(private CouponRepositoryInterface $coupons) {}

    public function index(): View
    {
        return view('admin.coupons.index', [
            'coupons' => $this->coupons->paginateAdmin(),
        ]);
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $this->coupons->create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(int $id): View
    {
        return view('admin.coupons.edit', ['coupon' => $this->coupons->find($id)]);
    }

    public function update(CouponRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $this->coupons->update($id, $data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->coupons->delete($id);

        return back()->with('success', 'Coupon deleted.');
    }
}
