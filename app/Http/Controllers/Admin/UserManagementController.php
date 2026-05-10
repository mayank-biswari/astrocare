<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagementFilterRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(UserManagementFilterRequest $request)
    {
        try {
            $query = User::with('roles');

            // Search by name or email (case-insensitive partial match)
            if ($search = $request->validated('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                      ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }

            // Filter by role (via Spatie roles relationship)
            if ($role = $request->validated('role')) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('roles.name', $role);
                });
            }

            // Filter by date range
            if ($dateFrom = $request->validated('date_from')) {
                $query->whereDate('users.created_at', '>=', $dateFrom);
            }
            if ($dateTo = $request->validated('date_to')) {
                $query->whereDate('users.created_at', '<=', $dateTo);
            }

            // Sort
            $sortBy = $request->validated('sort_by') ?? 'created_at';
            $sortDir = $request->validated('sort_dir') ?? 'desc';

            if ($sortBy === 'role') {
                // Sort by role requires a join
                $query->leftJoin('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                         ->where('model_has_roles.model_type', '=', User::class);
                })
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderByRaw("LOWER(roles.name) {$sortDir}")
                ->select('users.*');
            } else {
                $query->orderByRaw("LOWER(users.{$sortBy}) {$sortDir}");
            }

            // Get total count before pagination
            $totalFiltered = $query->count();

            // Paginate and append query parameters
            $users = $query->paginate(20)->appends($request->validated());
            $roles = Role::all();

            return view('admin.user-management.index', compact(
                'users', 'roles', 'totalFiltered'
            ));
        } catch (QueryException $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while loading users. Please try again.')
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while loading users. Please try again.')
                ->withInput();
        }
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.user-management.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.user-management.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user-management.index')->with('success', 'User deleted successfully');
    }

    public function roles()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.user-management.roles', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles']);
        Role::create(['name' => $request->name]);
        return back()->with('success', 'Role created successfully');
    }

    public function destroyRole(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Role deleted successfully');
    }
}
