<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Try exact match first, then case-insensitive match
            $user = User::with('role')->where('email', $request->email)->first();
            
            // If not found, try case-insensitive search
            if (!$user) {
                $user = User::with('role')->whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Unknown database')) {
                return back()->withErrors([
                    'email' => 'Database not found. Please run: php artisan db:setup'
                ])->withInput();
            }
            throw $e;
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check user status
        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Your account is ' . $user->status . '. Please contact administrator.'],
            ]);
        }

        // Login user
        Auth::login($user);

        // Update last login
        $user->updateLastLogin();

        // Reload user with role relationship to ensure isSuperAdmin() works correctly
        $user->load('role');

        // Handle branch selection for all users (including Super Admin)
        // Super Admin should have Main Branch assigned, but can access all branches
            $branches = $user->branches()->where('is_active', true)->get();
            
            if ($branches->count() === 0) {
            // If no branches assigned, try to get Main Branch for Super Admin
            if ($user->isSuperAdmin()) {
                $mainBranch = Branch::where('code', 'MB001')->where('is_active', true)->first();
                if ($mainBranch) {
                    // Assign Main Branch to Super Admin
                    $user->branches()->sync([$mainBranch->id]);
                    $branches = collect([$mainBranch]);
                } else {
                    // Fallback: get first active branch
                    $defaultBranch = Branch::where('is_active', true)->orderBy('id')->first();
                    if ($defaultBranch) {
                        $user->branches()->sync([$defaultBranch->id]);
                        $branches = collect([$defaultBranch]);
                    }
                }
            }
            
            // If still no branches and not Super Admin, show error
            if ($branches->count() === 0 && !$user->isSuperAdmin()) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'No active branches assigned to your account. Please contact administrator.');
            }
            }
            
        // Set active branch (for Super Admin, prefer Main Branch if assigned)
        if ($branches->count() > 0) {
            $defaultBranch = $branches->firstWhere('code', 'MB001') ?? $branches->first();
            \Illuminate\Support\Facades\Session::put('active_branch_id', $defaultBranch->id);
            \Illuminate\Support\Facades\Session::put('active_branch_name', $defaultBranch->name);
            \Illuminate\Support\Facades\Session::save();
        } elseif ($user->isSuperAdmin()) {
            // Super Admin fallback: get first active branch in system
        $defaultBranch = Branch::where('is_active', true)->orderBy('id')->first();
        if ($defaultBranch) {
            \Illuminate\Support\Facades\Session::put('active_branch_id', $defaultBranch->id);
            \Illuminate\Support\Facades\Session::put('active_branch_name', $defaultBranch->name);
            \Illuminate\Support\Facades\Session::save();
            }
        }

        return redirect()->route('dashboard')->with('success', 'Login successful!');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
