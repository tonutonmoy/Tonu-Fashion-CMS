<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function quickLogin(): RedirectResponse
    {
        if (! config('admin.quick_login_enabled')) {
            abort(404);
        }

        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $user = User::query()
            ->where('email', config('admin.email'))
            ->first();

        if (! $user || ! $user->isAdmin() || ! $user->isAccountActive()) {
            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Auto login is unavailable. Use your admin email and password.']);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        if (! Auth::user()->isAdmin()) {
            Auth::logout();

            return back()->withErrors(['email' => 'Access denied. Admin only.']);
        }

        if (! Auth::user()->isAccountActive()) {
            Auth::logout();

            return back()->withErrors(['email' => 'Your admin account is inactive.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
}
