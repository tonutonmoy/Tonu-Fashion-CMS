<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminOrderRequest;
use App\Http\Requests\Admin\UpdateCustomerRestrictionsRequest;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\AdminOrderService;
use App\Services\CourierSettingsService;
use App\Services\CourierManager;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private OrderService $orderService,
        private AdminOrderService $adminOrders,
        private UserRepositoryInterface $users,
        private CourierSettingsService $courierSettings,
        private CourierManager $courierManager,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->all();
        if (! $request->has('scope')) {
            $filters['scope'] = 'today';
        }

        return view('admin.orders.index', [
            'orders' => $this->orders->paginateAdmin($filters),
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.orders.create', [
            'products' => Product::query()
                ->with(['variants' => fn ($q) => $q->where('status', RecordStatus::Active)])
                ->where('status', RecordStatus::Active)
                ->orderBy('name')
                ->get(),
            'customers' => User::query()->where('role', UserRole::Customer)->orderBy('name')->get(['id', 'name', 'email', 'phone']),
            'statuses' => OrderStatus::cases(),
            'paymentMethods' => PaymentMethod::cases(),
            'divisions' => array_keys(config('bangladesh.divisions', [])),
        ]);
    }

    public function store(StoreAdminOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->adminOrders->createManualOrder($request->validated());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Custom order created successfully.');
    }

    public function show(string $orderNumber): View
    {
        $order = $this->orders->findByOrderNumber($orderNumber);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => OrderStatus::cases(),
            'customerAccount' => $order->user_id ? $this->users->find($order->user_id) : null,
            'activeCouriers' => $this->courierSettings->activeConfiguredCouriers($this->courierManager),
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        abort_unless(auth()->user()?->role->canManageOrders(), 403);

        $request->validate(['status' => 'required|in:'.implode(',', array_column(OrderStatus::cases(), 'value'))]);

        $order = $this->orders->find($id);
        abort_unless($order, 404);

        $status = OrderStatus::from($request->status);

        if ($order->status === $status) {
            return back()->with('status', 'Order status is already '.$status->label().'.');
        }

        try {
            $this->orderService->updateStatus($id, $status);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Could not update order status. Please try again.');
        }

        return back()->with('success', 'Order status updated.');
    }

    public function updateCustomerRestrictions(UpdateCustomerRestrictionsRequest $request, int $user): RedirectResponse
    {
        $customer = $this->users->find($user);

        abort_if(! $customer || ! $customer->isCustomer(), 404);

        $this->users->update($user, [
            ...$request->validated(),
            'status' => \App\Enums\RecordStatus::from($request->status),
        ]);

        return back()->with('success', 'Customer restrictions updated.');
    }
}
