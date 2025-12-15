<?php

namespace App\Http\Controllers;

use App\Models\RolePermissionAudit;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionAuditController extends Controller
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
     * Display audit trail for roles and permissions
     */
    public function index(Request $request)
    {
        $query = RolePermissionAudit::with(['role', 'permission', 'changedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $audits = $query->paginate(50);
        $roles = Role::orderBy('name')->get();

        return view('masters.roles.audit', compact('audits', 'roles'));
    }

    /**
     * Show audit details for a specific role
     */
    public function showRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $audits = RolePermissionAudit::where('role_id', $roleId)
            ->with(['permission', 'changedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('masters.roles.audit', compact('audits', 'role'));
    }

    /**
     * Generate report on role assignments and permissions
     */
    public function report(Request $request)
    {
        $query = User::with(['roles.permissions']);

        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get();

        $report = [];
        foreach ($users as $user) {
            $userPermissions = [];
            foreach ($user->roles as $role) {
                foreach ($role->permissions as $permission) {
                    $formName = $permission->form_name;
                    if (!isset($userPermissions[$formName])) {
                        $userPermissions[$formName] = [
                            'read' => false,
                            'write' => false,
                            'delete' => false,
                        ];
                    }
                    // Merge permissions (user gets highest permission from any role)
                    $userPermissions[$formName]['read'] = $userPermissions[$formName]['read'] || ($permission->pivot->read ?? false);
                    $userPermissions[$formName]['write'] = $userPermissions[$formName]['write'] || ($permission->pivot->write ?? false);
                    $userPermissions[$formName]['delete'] = $userPermissions[$formName]['delete'] || ($permission->pivot->delete ?? false);
                }
            }

            $report[] = [
                'user' => $user,
                'roles' => $user->roles->pluck('name')->toArray(),
                'permissions' => $userPermissions,
            ];
        }

        return view('masters.roles.report', compact('report'));
    }
}
