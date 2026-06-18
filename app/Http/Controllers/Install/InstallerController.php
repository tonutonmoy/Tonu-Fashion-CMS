<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Http\Requests\Install\AdminAccountRequest;
use App\Http\Requests\Install\DatabaseConfigRequest;
use App\Http\Requests\Install\StoreInfoRequest;
use App\Services\InstallerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallerController extends Controller
{
    public function __construct(private InstallerService $installer) {}

    public function requirements(): View|RedirectResponse
    {
        try {
            $this->installer->ensureEnvFile();
        } catch (\Throwable $e) {
            return view('install.requirements', [
                'checks' => [[
                    'label' => '.env Bootstrap',
                    'passed' => false,
                    'detail' => $e->getMessage(),
                ]],
                'passed' => false,
            ]);
        }

        $checks = $this->installer->checkRequirements();
        $passed = $this->installer->requirementsPassed();

        return view('install.requirements', compact('checks', 'passed'));
    }

    public function database(): View|RedirectResponse
    {
        if (! $this->installer->requirementsPassed()) {
            return redirect()->route('install.requirements');
        }

        $session = $this->installer->getSessionData()['database'] ?? [];

        return view('install.database', [
            'config' => array_merge([
                'db_host' => '127.0.0.1',
                'db_port' => 3306,
                'db_database' => '',
                'db_username' => '',
                'db_password' => '',
            ], $session),
        ]);
    }

    public function storeDatabase(DatabaseConfigRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $test = $this->installer->testDatabase($data);

        if (! $test['success']) {
            return back()->withInput()->with('error', $test['message']);
        }

        try {
            $this->installer->saveDatabaseConfig($data);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('install.store')->with('success', 'Database configuration saved.');
    }

    public function testDatabase(DatabaseConfigRequest $request): JsonResponse
    {
        $result = $this->installer->testDatabase($request->validated());

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function store(): View|RedirectResponse
    {
        if (empty($this->installer->getSessionData()['database'])) {
            return redirect()->route('install.database');
        }

        $session = $this->installer->getSessionData()['store'] ?? [];
        $currencies = $this->installer->currencies();

        return view('install.store', [
            'data' => array_merge([
                'store_name' => 'Fashion BD',
                'store_email' => '',
                'phone' => '01',
                'address' => 'Dhaka, Bangladesh',
                'currency_code' => 'BDT',
                'currency_symbol' => '৳',
                'timezone' => 'Asia/Dhaka',
                'default_theme' => config('themes.default'),
                'app_url' => url('/'),
            ], $session),
            'themes' => $this->installer->availableThemes(),
            'timezones' => $this->installer->timezones(),
            'currencies' => $currencies,
        ]);
    }

    public function saveStore(StoreInfoRequest $request): RedirectResponse
    {
        $this->installer->saveStoreConfig($request->validated());

        return redirect()->route('install.admin');
    }

    public function admin(): View|RedirectResponse
    {
        if (empty($this->installer->getSessionData()['store'])) {
            return redirect()->route('install.store');
        }

        $session = $this->installer->getSessionData()['admin'] ?? [];

        return view('install.admin', [
            'data' => array_merge([
                'name' => config('admin.name'),
                'email' => config('admin.email'),
                'phone' => config('admin.phone'),
            ], $session),
        ]);
    }

    public function saveAdmin(AdminAccountRequest $request): RedirectResponse
    {
        $this->installer->saveAdminConfig($request->validated());

        return redirect()->route('install.run');
    }

    public function run(): View|RedirectResponse
    {
        $session = $this->installer->getSessionData();

        if (empty($session['database']) || empty($session['store']) || empty($session['admin'])) {
            return redirect()->route('install.requirements');
        }

        return view('install.run', [
            'summary' => $session,
        ]);
    }

    public function execute(Request $request): RedirectResponse|View
    {
        try {
            $log = $this->installer->runInstallation();
        } catch (\Throwable $e) {
            return back()->with('error', 'Installation failed: '.$e->getMessage());
        }

        return view('install.complete', compact('log'));
    }
}
