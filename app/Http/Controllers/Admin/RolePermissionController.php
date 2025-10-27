<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::all();
            $permissions = Permission::all();
            return view('admin.role_permission.index', compact('roles', 'permissions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load roles and permissions: ' . $e->getMessage());
        }
    }

    public function assign(Request $request): RedirectResponse
    {
        try {
            $permissions = $request->input('permissions', []);

            $roles = Role::all();
            $permissions_all = Permission::all();

            foreach ($roles as $role) {
                foreach ($permissions_all as $permission) {
                    $assign = isset($permissions[$role->id][$permission->id]) ? true : false;

                    if ($assign && !$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    } elseif (!$assign && $role->hasPermissionTo($permission)) {
                        $role->revokePermissionTo($permission);
                    }
                }
            }

            return redirect()->back()->with('success', 'Permissions updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }


    public function createRole(Request $request)
    {
        try {
            $request->validate(['name' => 'required|unique:roles,name']);
            Role::create(['name' => $request->name]);
            return redirect()->back()->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    public function createPermission(Request $request)
    {
        try {
            $request->validate(['name' => 'required|unique:permissions,name']);
            Permission::create(['name' => $request->name]);
            return redirect()->back()->with('success', 'Permission created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create permission: ' . $e->getMessage());
        }
    }
}
