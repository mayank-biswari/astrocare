<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.user-roles.index', compact('users'));
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        $userRoleIds = $user->roles->pluck('id')->toArray();

        return view('admin.user-roles.edit', compact('user', 'roles', 'userRoleIds'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $roleIds = array_map('intval', $validated['roles'] ?? []);

        // Self-lockout prevention: prevent admin from removing their own admin role
        if ($user->id === auth()->id()) {
            $adminRole = Role::where('name', 'admin')->first();

            if ($adminRole && !in_array($adminRole->id, $roleIds)) {
                return redirect()->back()->with('error', 'You cannot remove the admin role from your own account.');
            }
        }

        $user->syncRoles($roleIds);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.user-roles.index')->with('success', 'User roles updated successfully.');
    }
}
