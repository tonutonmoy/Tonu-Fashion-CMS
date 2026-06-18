<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCustomerRestrictionsRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function index(Request $request): View
    {
        return view('admin.customers.index', [
            'customers' => $this->users->paginateCustomers($request->all()),
        ]);
    }

    public function show(int $id): View
    {
        $customer = $this->users->find($id);

        abort_if(! $customer || ! $customer->isCustomer(), 404);

        $customer->load(['addresses']);

        return view('admin.customers.show', [
            'customer' => $customer,
            'orders' => $customer->orders()->with('items')->latest()->paginate(admin_per_page())->withQueryString(),
        ]);
    }

    public function updateRestrictions(UpdateCustomerRestrictionsRequest $request, int $id): RedirectResponse
    {
        $customer = $this->users->find($id);

        abort_if(! $customer || ! $customer->isCustomer(), 404);

        $this->users->update($id, [
            ...$request->validated(),
            'status' => \App\Enums\RecordStatus::from($request->status),
        ]);

        return back()->with('success', 'Customer restrictions updated.');
    }
}
