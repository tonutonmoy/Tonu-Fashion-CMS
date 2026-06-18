<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingsRequest;
use App\Services\ImageService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private SettingService $settings,
        private ImageService $images
    ) {}

    public function storeSettings(): View
    {
        return view('admin.settings.store', [
            'settings' => $this->settings->getGroup('store'),
        ]);
    }

    public function updateStoreSettings(StoreSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        foreach (['logo', 'favicon', 'og_image'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->images->upload(
                    $request->file($field),
                    'settings',
                    $field === 'favicon' ? 64 : 800
                );
            } else {
                unset($data[$field]);
            }
        }

        $this->settings->updateStore($data);

        return back()->with('success', 'Store settings updated.');
    }

}
