<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamUserRequest;
use App\Http\Requests\Admin\UpdateTeamUserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeamUserController extends Controller
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function index(Request $request): View
    {
        return view('admin.users.index', [
            'users' => $this->users->paginateTeamMembers($request->all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => UserRole::assignableTeamRoles(),
        ]);
    }

    public function store(StoreTeamUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->users->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => UserRole::from($data['role']),
            'status' => RecordStatus::Active,
            'order_blocked' => false,
            'blog_blocked' => false,
            'admin_permissions' => $data['permissions'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Team member created successfully.');
    }

    public function edit(int $user): View
    {
        $member = $this->users->find($user);

        abort_if(! $member || ! in_array($member->role, UserRole::assignableTeamRoles(), true), 404);

        return view('admin.users.edit', [
            'member' => $member,
            'roles' => UserRole::assignableTeamRoles(),
        ]);
    }

    public function update(UpdateTeamUserRequest $request, int $user): RedirectResponse
    {
        $member = $this->users->find($user);

        abort_if(! $member || ! in_array($member->role, UserRole::assignableTeamRoles(), true), 404);

        $data = $request->validated();
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => UserRole::from($data['role']),
            'status' => RecordStatus::from($data['status']),
            'admin_permissions' => $data['permissions'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $this->users->update($user, $payload);

        return redirect()->route('admin.users.index')->with('success', 'Team member updated.');
    }

    public function destroy(int $user): RedirectResponse
    {
        $member = $this->users->find($user);

        abort_if(! $member || ! in_array($member->role, UserRole::assignableTeamRoles(), true), 404);

        if ($member->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $this->users->delete($user);

        return redirect()->route('admin.users.index')->with('success', 'Team member removed.');
    }
}
