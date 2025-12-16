<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show OTP verification form
     */
    public function show()
    {
        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Get OTP for auto-fill
        $otp = $this->otpService->getLatestOtp($user);

        return view('auth.otp', compact('otp'));
    }

    /**
     * Verify OTP
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Verify OTP
        if (!$this->otpService->verifyOtp($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.'])->withInput();
        }

        // Check user status
        if (!$user->isActive()) {
            return back()->withErrors(['otp' => 'Your account is ' . $user->status . '. Please contact administrator.'])->withInput();
        }

        // Login user
        Auth::login($user);

        // Update last login
        $user->updateLastLogin();

        // Clear OTP session
        session()->forget('otp_user_id');

        // Reload user with role relationship to ensure isSuperAdmin() works correctly
        $user->load('role');

        // Handle branch selection for all users (including Super Admin)
        // Super Admin should have Main Branch assigned, but can access all branches
            $branches = $user->branches()->where('is_active', true)->get();
            
            if ($branches->count() === 0) {
            // If no branches assigned, try to get Main Branch for Super Admin
            if ($user->isSuperAdmin()) {
                $mainBranch = \App\Models\Branch::where('code', 'MB001')->where('is_active', true)->first();
                if ($mainBranch) {
                    // Assign Main Branch to Super Admin
                    $user->branches()->sync([$mainBranch->id]);
                    $branches = collect([$mainBranch]);
                } else {
                    // Fallback: get first active branch
                    $defaultBranch = \App\Models\Branch::where('is_active', true)->orderBy('id')->first();
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
            session(['active_branch_id' => $defaultBranch->id]);
            session(['active_branch_name' => $defaultBranch->name]);
            
            if ($branches->count() === 1) {
                // Single branch - auto-select
                return redirect()->route('dashboard')->with('success', 'Login successful!');
            } else {
                // Multiple branches - redirect to branch selection
                return redirect()->route('branch.select')->with('success', 'Please select a branch to continue.');
            }
        } elseif ($user->isSuperAdmin()) {
            // Super Admin fallback: get first active branch in system
            $defaultBranch = \App\Models\Branch::where('is_active', true)->orderBy('id')->first();
            if ($defaultBranch) {
                session(['active_branch_id' => $defaultBranch->id]);
                session(['active_branch_name' => $defaultBranch->name]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Login successful!');
    }
}
