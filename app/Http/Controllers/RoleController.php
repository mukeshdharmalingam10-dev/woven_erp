<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermissionAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Ensure only super admin or authorized users can manage roles
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $roles = Role::paginate(15);
        return view('masters.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('masters.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'description' => 'nullable|string',
        ]);

        // Generate slug from name
        $slug = Str::slug($request->name);
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        // Log audit trail
        RolePermissionAudit::log('created', $role->id, null, null, null, json_encode($request->all()), "Role '{$role->name}' created");

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('masters.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
        ]);

        // Store old values for audit
        $oldName = $role->name;
        $oldDescription = $role->description;
        $oldSlug = $role->slug;

        // Generate slug from name if name changed
        $slug = $role->slug;
        if ($oldName != $request->name) {
            $slug = Str::slug($request->name);
            
            // Ensure slug is unique (excluding current role)
            $originalSlug = $slug;
            $counter = 1;
            while (Role::where('slug', $slug)->where('id', '!=', $role->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $role->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        // Log audit trail for each changed field
        if ($oldName != $request->name) {
            RolePermissionAudit::log('updated', $role->id, null, 'name', $oldName, $request->name, "Role name changed");
        }
        if ($oldSlug != $slug) {
            RolePermissionAudit::log('updated', $role->id, null, 'slug', $oldSlug, $slug, "Role slug changed");
        }
        if ($oldDescription != $request->description) {
            RolePermissionAudit::log('updated', $role->id, null, 'description', $oldDescription, $request->description, "Role description changed");
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Log audit trail before deletion
        RolePermissionAudit::log('deleted', $role->id, null, null, json_encode($role->toArray()), null, "Role '{$role->name}' deleted");

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
