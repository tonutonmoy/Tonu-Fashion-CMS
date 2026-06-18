<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignLicenseDomainRequest;
use App\Http\Requests\Admin\UpdateLicenseRequest;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use App\Services\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function __construct(
        private LicenseRepositoryInterface $licenses,
        private LicenseService $licenseService,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.license.index', [
            'licenses' => $this->licenses->paginateAdmin($request->only(['search', 'status'])),
            'stats' => $this->licenseService->stats(),
            'currentDomain' => $this->licenseService->normalizeDomain($request->getHost()),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function edit(int $id): View
    {
        return view('admin.license.edit', [
            'license' => $this->licenses->find($id),
        ]);
    }

    public function update(UpdateLicenseRequest $request, int $id): RedirectResponse
    {
        $this->licenseService->updateLicense($id, $request->validated());

        return redirect()->route('admin.license.index')->with('success', 'License updated.');
    }

    public function assignDomain(AssignLicenseDomainRequest $request, int $id): RedirectResponse
    {
        $this->licenseService->assignDomain($id, $request->licensed_domain);

        return back()->with('success', 'Domain assigned successfully.');
    }

    public function suspend(int $id): RedirectResponse
    {
        $this->licenseService->suspend($id);

        return back()->with('success', 'License suspended.');
    }

    public function expire(int $id): RedirectResponse
    {
        $this->licenseService->expire($id);

        return back()->with('success', 'License marked as expired.');
    }

    public function activate(int $id): RedirectResponse
    {
        $this->licenseService->activate($id);

        return back()->with('success', 'License activated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->licenses->delete($id);

        return back()->with('success', 'License deleted.');
    }
}
