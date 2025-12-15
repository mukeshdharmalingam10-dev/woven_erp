<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckBranchAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin bypasses branch filtering
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has active branch in session
        $activeBranchId = Session::get('active_branch_id');
        
        if (!$activeBranchId) {
            // Redirect to branch selection
            return redirect()->route('branch.select')
                ->with('error', 'Please select a branch to continue.');
        }

        // Verify user has access to the active branch
        if (!$user->hasAccessToBranch($activeBranchId)) {
            Session::forget('active_branch_id');
            Session::forget('active_branch_name');
            return redirect()->route('branch.select')
                ->with('error', 'You do not have access to the selected branch.');
        }

        return $next($request);
    }
}
