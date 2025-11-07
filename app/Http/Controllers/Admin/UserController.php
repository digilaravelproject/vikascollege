<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('view users');
            $users = User::with('roles')->latest()->get();
            return view('admin.users.index', compact('users'));
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to load users: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('create users');
            $roles = Role::all();
            return view('admin.users.create', compact('roles'));
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to load roles: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create users');

            // Validation
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'roles' => ['nullable', 'array'],
                'roles.*' => ['exists:roles,name'],
            ]);

            // User Create
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Role Assign (if roles are selected)
            if ($request->has('roles')) {
                $user->assignRole($request->roles);
            }

            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        try {
            $this->authorize('edit users');
            $roles = Role::all();
            $userRoles = $user->roles->pluck('name')->toArray(); // Get current roles of the user

            return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')->with('error', 'User not found: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to load user for editing: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $this->authorize('edit users');

            // Validation
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user->id],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'roles' => ['nullable', 'array'],
                'roles.*' => ['exists:roles,name'],
            ]);

            // Prepare data for update
            $data = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Update User
            $user->update($data);

            // Sync roles (removes existing roles and assigns new ones)
            $user->syncRoles($request->roles);

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Attempt to delete the user
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
