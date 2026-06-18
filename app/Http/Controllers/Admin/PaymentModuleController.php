<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentSettingsRequest;
use App\Services\PaymentSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentModuleController extends Controller
{
    public function __construct(private PaymentSettingsService $settings) {}

    public function index(): View
    {
        return view('admin.payment.index', [
            'settings' => $this->settings->all(),
        ]);
    }

    public function update(PaymentSettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());

        return back()->with('success', 'Payment gateway settings saved.');
    }
}
