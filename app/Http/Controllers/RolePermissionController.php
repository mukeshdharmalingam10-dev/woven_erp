<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Menu;
use App\Models\RoleFormPermission;
use App\Models\Permission;
use App\Models\RolePermissionAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Show role permissions list with permission counts
     * Only shows roles that have at least one permission assigned
     * Excludes Super Admin role (has access to all forms by default)
     */
    public function select()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        
        // Calculate permission count for each role (permissions with at least one flag set)
        $roles->each(function($role) {
            $role->permission_count = $role->permissions->filter(function($permission) {
                return ($permission->pivot->read ?? false) || 
                       ($permission->pivot->write ?? false) || 
                       ($permission->pivot->delete ?? false);
            })->count();
        });
        
        // Filter to only show roles with permissions assigned, excluding Super Admin
        $roles = $roles->filter(function($role) {
            // Exclude Super Admin role (has access to all forms by default)
            if ($role->slug === 'super-admin') {
                return false;
            }
            return $role->permission_count > 0;
        });
        
        return view('masters.roles.select-role', compact('roles'));
    }

    /**
     * Show form to select role for permission assignment
     * Excludes Super Admin role and roles that already have permissions assigned
     */
    public function create()
    {
        // Get all roles with their permissions
        $allRoles = Role::with('permissions')->orderBy('name')->get();
        
        // Filter out Super Admin and roles that already have permissions assigned
        $roles = $allRoles->filter(function($role) {
            // Exclude Super Admin role (has access to all forms by default)
            if ($role->slug === 'super-admin') {
                return false;
            }
            
            // Exclude roles that already have permissions assigned
            $hasPermissions = $role->permissions->filter(function($permission) {
                return ($permission->pivot->read ?? false) || 
                       ($permission->pivot->write ?? false) || 
                       ($permission->pivot->delete ?? false);
            })->count() > 0;
            
            return !$hasPermissions; // Only include roles without permissions
        });
        
        // Get all active permissions, ensuring we get all of them
        $permissions = Permission::where('is_active', true)
            ->orderByRaw('COALESCE(form_name, name) ASC')
            ->get();
        
        return view('masters.roles.assign-permissions', compact('roles', 'permissions'));
    }

    /**
     * Handle role selection and redirect to edit form
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        return redirect()->route('role-permissions.edit', $request->role_id);
    }

    /**
     * Show permission assignment form for selected role
     * Excludes Super Admin role from dropdown
     */
    public function edit(Role $role)
    {
        // Prevent editing Super Admin role permissions
        if ($role->slug === 'super-admin') {
            abort(403, 'Super Admin role has access to all forms by default and cannot be modified.');
        }
        
        // Define module order
        $moduleOrder = [
            'System Admin',
            'Settings',
            'Company-info',
            'Customer-complaints',
            'Masters',
            'Transactions',
            'Productions',
            'CRM'
        ];
        
        // Load all permissions from permissions table grouped by module
        $permissions = Permission::where('is_active', true)
            ->orderBy('form_name')
            ->get()
            ->groupBy('module')
            ->sortBy(function ($group, $key) use ($moduleOrder) {
                $index = array_search($key, $moduleOrder);
                return $index !== false ? $index : 999; // Put unknown modules at the end
            });

        // Load existing permissions for this role from role_permission table
        $rolePermissions = $role->permissions()->get()->keyBy('id');

        // Roles for dropdown (still exclude Super Admin)
        $allRoles = Role::orderBy('name')->get()->filter(function ($r) use ($role) {
            if ($r->slug === 'super-admin') {
                return false;
            }
                return true;
        });

        return view('masters.roles.permissions', [
            'role'               => $role,
            'permissionsByModule'=> $permissions,
            'rolePermissions'    => $rolePermissions,
            'allRoles'           => $allRoles,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        // Prevent updating Super Admin role permissions
        if ($role->slug === 'super-admin') {
            abort(403, 'Super Admin role has access to all forms by default and cannot be modified.');
        }
        
        // Expecting input array: form_permissions[permission_id][read|write|delete] = 1/0
        $data = $request->input('form_permissions', []);
        
        $submittedPermissionIds = array_keys($data);
        $syncData = [];

        foreach ($data as $permissionId => $flags) {
            $read   = !empty($flags['read']);
            $write  = !empty($flags['write']);
            $delete = !empty($flags['delete']);
            
            // Enforce hierarchical permission logic:
            // - Write automatically includes Read (view + edit/add)
            // - Delete automatically includes Read + Write (full access)
            if ($write) {
                $read = true; // Write permission includes Read
            }
            if ($delete) {
                $read = true;  // Delete permission includes Read
                $write = true; // Delete permission includes Write
            }
            
            if ($read || $write || $delete) {
                $syncData[$permissionId] = [
                    'read' => $read ? 1 : 0,
                    'write' => $write ? 1 : 0,
                    'delete' => $delete ? 1 : 0,
                ];
            }
        }

        // Sync permissions - this will add/update/remove as needed
        $role->permissions()->sync($syncData);

        return redirect()
            ->route('role-permissions.edit', $role->id)
            ->with('success', 'Permissions for role "' . $role->name . '" updated successfully.');
    }
}
