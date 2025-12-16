<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a list of roles and permissions.
     */
    public function index()
    {
        $this->authorize('view roles'); // Ensure user has permission to view roles and permissions

        try {
            $roles = Role::all();
            $permissions = Permission::all();

            // Permissions grouping logic
            $categories = [
                'User & Role Management' => [
                    'view users',
                    'create users',
                    'edit users',
                    'delete users',
                    'view roles',
                    'create roles',
                    'edit roles',
                    'delete roles',
                    'assign roles',
                    'manage permissions'
                ],
                'Content Management' => [
                    'view pages',
                    'create pages',
                    'edit pages',
                    'delete pages',
                    'manage menus'
                ],
                'Announcements & Events' => [
                    'view announcements',
                    'create announcements',
                    'edit announcements',
                    'delete announcements',
                    'publish announcements',
                    'view events',
                    'create events',
                    'edit events',
                    'delete events',
                    'manage event categories'
                ],
                'Gallery' => [
                    'view gallery',
                    'upload gallery images',
                    'delete gallery images',
                    'manage gallery categories'
                ],
                'Academic & Info Sections' => [
                    'manage academic calendar',
                    'manage testimonials',
                    'approve testimonials',
                    'manage trust sections',
                    'manage why choose us'
                ],
                'System' => [
                    'manage settings',
                    'view admin dashboard'
                ]
            ];

            // Group permissions
            $groupedPermissions = [];
            $uncategorizedPermissionNames = $permissions->pluck('name')->all();

            foreach ($categories as $categoryName => $permissionNames) {
                $perms = $permissions->whereIn('name', $permissionNames);
                if ($perms->isNotEmpty()) {
                    $groupedPermissions[$categoryName] = $perms;
                    // Remove assigned permissions from uncategorized list
                    $uncategorizedPermissionNames = array_diff($uncategorizedPermissionNames, $permissionNames);
                }
            }

            // Add uncategorized permissions under "Other"
            if (!empty($uncategorizedPermissionNames)) {
                $groupedPermissions['Other'] = $permissions->whereIn('name', $uncategorizedPermissionNames);
            }

            return view('admin.role_permission.index', compact('roles', 'groupedPermissions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load roles and permissions: ' . $e->getMessage());
        }
    }

    /**
     * Handle auto and bulk assignment of permissions to roles.
     */
    public function assign(Request $request)
    {
        $this->authorize('manage permissions'); // Ensure user has permission to manage permissions

        try {
            // --- Case 1: Auto toggle update ---
            if ($request->has('auto')) {
                $role = Role::find($request->role_id);
                $permission = Permission::find($request->permission_id);

                if (!$role || !$permission) {
                    return response()->json(['success' => false, 'message' => 'Invalid data.'], 400);
                }

                if ($request->status) {
                    $role->givePermissionTo($permission);
                } else {
                    $role->revokePermissionTo($permission);
                }

                return response()->json(['success' => true]);
            }

            // --- Case 2: Bulk Save ---
            $permissionsInput = $request->input('permissions', []);
            $roles = Role::all();
            $permissionsAll = Permission::all();

            foreach ($roles as $role) {
                foreach ($permissionsAll as $permission) {
                    $assign = isset($permissionsInput[$role->id][$permission->id]);

                    if ($assign && !$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    } elseif (!$assign && $role->hasPermissionTo($permission)) {
                        $role->revokePermissionTo($permission);
                    }
                }
            }

            // Clear the cached permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->back()->with('success', 'Permissions updated successfully.');
        } catch (\Exception $e) {
            // If the request expects JSON (for auto-toggle)
            if ($request->has('auto')) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Create a new role.
     */
    public function createRole(Request $request)
    {
        $this->authorize('create roles'); // Ensure user has permission to create roles

        try {
            $request->validate(['name' => 'required|unique:roles,name']);

            Role::create(['name' => $request->name]);

            return redirect()->back()->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Create a new permission.
     */
    public function createPermission(Request $request)
    {
        $this->authorize('manage permissions'); // Ensure user has permission to manage permissions

        try {
            $request->validate(['name' => 'required|unique:permissions,name']);

            Permission::create(['name' => $request->name]);

            return redirect()->back()->with('success', 'Permission created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete a role.
     */
    public function deleteRole(Role $role)
    {
        $this->authorize('delete roles'); // Ensure user has permission to delete roles

        try {
            $role->delete();

            return redirect()->back()->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Delete a permission.
     */
    public function deletePermission(Permission $permission)
    {
        $this->authorize('delete permissions'); // Ensure user has permission to delete permissions

        try {
            $permission->delete();

            return redirect()->back()->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete permission: ' . $e->getMessage());
        }
    }
}
