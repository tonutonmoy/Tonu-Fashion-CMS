<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function index(): View
    {
        return view('frontend.orders.index', [
            'orders' => $this->orders->paginateForUser(Auth::id()),
        ]);
    }

    public function show(string $orderNumber): View
    {
        $order = $this->orders->findByOrderNumber($orderNumber);

        if (! $order || ($order->user_id && $order->user_id !== Auth::id())) {
            abort(404);
        }

        return view('frontend.orders.show', compact('order'));
    }
}
