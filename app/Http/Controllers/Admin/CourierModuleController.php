<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourierSettingsRequest;
use App\Services\ActivityLogService;
use App\Services\CourierSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourierModuleController extends Controller
{
    public function __construct(
        private CourierSettingsService $settings,
        private ActivityLogService $activity,
    ) {}

    public function index(): View
    {
        return view('admin.courier.index', [
            'settings' => $this->settings->all(),
        ]);
    }

    public function update(CourierSettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());
        $this->activity->log('courier.settings_updated', 'Courier settings updated');

        return back()->with('success', 'Courier settings saved.');
    }

    public function activity(): View
    {
        return view('admin.courier.activity', [
            'logs' => $this->activity->paginateAdmin(),
        ]);
    }
}
