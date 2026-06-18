<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user()->load('addresses');

        return view('frontend.profile.edit', compact('user'));
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'division' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'address_line' => 'required|string|max:500',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            Address::query()->where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::query()->create([...$data, 'user_id' => Auth::id()]);

        return back()->with('success', 'Address saved.');
    }
}
