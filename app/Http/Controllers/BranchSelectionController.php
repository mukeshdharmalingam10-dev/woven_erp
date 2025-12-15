<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class BranchSelectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show branch selection screen.
     */
    public function show()
    {
        $user = Auth::user();

        // Super Admin doesn't need branch selection
        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        // If already has active branch and only one branch, redirect to dashboard
        if (session('active_branch_id')) {
            $branches = $user->branches()->where('is_active', true)->get();
            if ($branches->count() === 1) {
                return redirect()->route('dashboard');
            }
        }

        $branches = $user->branches()->where('is_active', true)->get();

        if ($branches->count() === 0) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'No active branches assigned to your account. Please contact administrator.');
        }

        return view('auth.branch-select', compact('branches'));
    }

    /**
     * Handle branch selection.
     */
    public function select(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Super Admin doesn't need branch selection
        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $branch = Branch::findOrFail($request->branch_id);

        // Verify user has access to this branch
        if (!$user->hasAccessToBranch($branch->id)) {
            return back()->withErrors(['branch_id' => 'You do not have access to this branch.'])->withInput();
        }

        // Set active branch in session
        Session::put('active_branch_id', $branch->id);
        Session::put('active_branch_name', $branch->name);
        Session::save();

        return redirect()->route('dashboard')->with('success', 'Branch selected: ' . $branch->name);
    }

    /**
     * Switch to a different branch (for users with multiple branches).
     */
    public function switch(Request $request, Branch $branch)
    {
        $user = Auth::user();

        // Super Admin can switch to any branch
        if ($user->isSuperAdmin()) {
            Session::put('active_branch_id', $branch->id);
            Session::put('active_branch_name', $branch->name);
            Session::save();
            return redirect()->back()->with('success', 'Switched to branch: ' . $branch->name);
        }

        // Verify user has access to this branch and it's active
        if (!$user->hasAccessToBranch($branch->id)) {
            return redirect()->back()->with('error', 'You do not have access to this branch.');
        }

        if (!$branch->is_active) {
            return redirect()->back()->with('error', 'This branch is not active.');
        }

        // Set active branch in session
        Session::put('active_branch_id', $branch->id);
        Session::put('active_branch_name', $branch->name);
        Session::save();

        // Redirect to dashboard to refresh all data
        return redirect()->route('dashboard')->with('success', 'Switched to branch: ' . $branch->name);
    }

    /**
     * Clear the active branch (redirects to branch selection).
     */
    public function clear(): RedirectResponse
    {
        $user = Auth::user();

        // Super Admin doesn't need branch selection
        if ($user->isSuperAdmin()) {
            Session::forget('active_branch_id');
            Session::forget('active_branch_name');
            return redirect()->route('dashboard')->with('success', 'Branch context cleared.');
        }

        Session::forget('active_branch_id');
        Session::forget('active_branch_name');

        return redirect()->route('branch.select')->with('success', 'Please select a branch to continue.');
    }
}
