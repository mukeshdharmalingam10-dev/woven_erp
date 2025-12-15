<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
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

    public function index()
    {
        // Use form_name if exists, otherwise fallback to name
        $permissions = Permission::orderByRaw('COALESCE(form_name, name)')->paginate(15);
        return view('masters.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('masters.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'form_name' => 'required|string|unique:permissions,form_name|max:255',
        ]);

        Permission::create($request->all());

        return redirect()->route('permissions.index')->with('success', 'Form/Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('masters.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'form_name' => 'required|string|max:255|unique:permissions,form_name,' . $permission->id,
        ]);

        $permission->update($request->all());

        return redirect()->route('permissions.index')->with('success', 'Form/Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Form/Permission deleted successfully.');
    }
}
