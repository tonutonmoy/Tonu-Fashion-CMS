<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BuilderPublishService;
use App\Services\ThemeCustomizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WebsiteBuilderController extends Controller
{
    public function __construct(
        private ThemeCustomizerService $customizer,
        private BuilderPublishService $publish
    ) {}

    public function index(): View
    {
        return view('admin.builder.index', [
            'settings' => $this->customizer->get(),
        ]);
    }

    public function publish(): RedirectResponse
    {
        if (! $this->publish->hasUnpublishedChanges()) {
            return back()->with('status', 'Nothing to publish — your live site is already up to date.');
        }

        $this->publish->publish();

        return back()->with('success', 'Published! Your changes are now live on the storefront.');
    }
}
