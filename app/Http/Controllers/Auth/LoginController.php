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
            $user = User::with('role')->where('email', $request->email)->first();
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

        // Handle branch selection for all non-Super Admin users
        if (!$user->isSuperAdmin()) {
            $branches = $user->branches()->where('is_active', true)->get();
            
            if ($branches->count() === 0) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'No active branches assigned to your account. Please contact administrator.');
            }
            
            // Auto-select first branch (default branch) - user can switch later if needed
            $defaultBranch = $branches->first();
            \Illuminate\Support\Facades\Session::put('active_branch_id', $defaultBranch->id);
            \Illuminate\Support\Facades\Session::put('active_branch_name', $defaultBranch->name);
            \Illuminate\Support\Facades\Session::save();
            
            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }

        // Super Admin - set default active branch as the first active branch in the system (if any)
        $defaultBranch = Branch::where('is_active', true)->orderBy('id')->first();
        if ($defaultBranch) {
            \Illuminate\Support\Facades\Session::put('active_branch_id', $defaultBranch->id);
            \Illuminate\Support\Facades\Session::put('active_branch_name', $defaultBranch->name);
            \Illuminate\Support\Facades\Session::save();
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
