<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Menu;
use App\Models\RoleFormPermission;
use App\Models\Permission;
use App\Models\RolePermissionAudit;
use Illuminate\Http\Request;

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
        
        // Load all active menus with submenus and forms (new Menu/Submenu/Form structure)
        $menus = Menu::with(['submenus' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }, 'forms' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }, 'submenus.forms' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->where('is_active', true)
            ->orderBy('name')
                ->get();

        // Load existing form permissions for this role, key by form_id
        $roleFormPermissions = RoleFormPermission::where('role_id', $role->id)
            ->get()
            ->keyBy('form_id');

        // Roles for dropdown (still exclude Super Admin)
        $allRoles = Role::orderBy('name')->get()->filter(function ($r) use ($role) {
            if ($r->slug === 'super-admin') {
                return false;
            }
                return true;
        });

        return view('masters.roles.permissions', [
            'role'               => $role,
            'menus'              => $menus,
            'roleFormPermissions'=> $roleFormPermissions,
            'allRoles'           => $allRoles,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        // Prevent updating Super Admin role permissions
        if ($role->slug === 'super-admin') {
            abort(403, 'Super Admin role has access to all forms by default and cannot be modified.');
        }
        
        // Expecting input array: form_permissions[form_id][read|write|delete] = 1/0
        $data = $request->input('form_permissions', []);
        
        $submittedFormIds = array_keys($data);

        foreach ($data as $formId => $flags) {
            $read   = !empty($flags['read']);
            $write  = !empty($flags['write']);
            $delete = !empty($flags['delete']);
            
            if (!$read && !$write && !$delete) {
                // No access selected -> remove any existing permission
                RoleFormPermission::where('role_id', $role->id)
                    ->where('form_id', $formId)
                    ->delete();
                continue;
            }

            // Map checkbox combination to composite permission_type
            if ($delete) {
                $permissionType = RoleFormPermission::FULL_ACCESS;
            } elseif ($write) {
                $permissionType = RoleFormPermission::ADD_EDIT_UPDATE;
            } else {
                // Only read checked
                $permissionType = RoleFormPermission::VIEW;
            }

            RoleFormPermission::updateOrCreate(
                ['role_id' => $role->id, 'form_id' => $formId],
                ['permission_type' => $permissionType]
            );
        }

        // Any existing permissions for forms not present in submitted data are removed (no access)
        if (!empty($submittedFormIds)) {
            RoleFormPermission::where('role_id', $role->id)
                ->whereNotIn('form_id', $submittedFormIds)
                ->delete();
        }

        return redirect()
            ->route('role-permissions.edit', $role->id)
            ->with('success', 'Permissions for role "' . $role->name . '" updated successfully.');
    }
}
