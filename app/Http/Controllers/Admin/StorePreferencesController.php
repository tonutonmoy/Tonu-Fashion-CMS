<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StorePreferencesController extends Controller
{
    public function __construct(private SettingService $settings) {}

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('settings'), 403);

        $request->validate([
            'default_locale' => ['required', 'in:'.implode(',', config('locales.supported', ['en', 'bn']))],
            'default_color_mode' => ['required', 'in:'.implode(',', config('locales.color_modes', ['light', 'dark']))],
        ]);

        $this->settings->updateStore([
            'default_locale' => $request->input('default_locale'),
            'default_color_mode' => $request->input('default_color_mode'),
        ]);

        return back()->with('success', __('admin.preferences_saved'));
    }
}
