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

        // Handle branch selection for non-Super Admin users
        if (!$user->isSuperAdmin()) {
            $branches = $user->branches()->where('is_active', true)->get();
            
            if ($branches->count() === 0) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'No active branches assigned to your account. Please contact administrator.');
            }
            
            if ($branches->count() === 1) {
                // Auto-select the single branch
                session(['active_branch_id' => $branches->first()->id]);
                session(['active_branch_name' => $branches->first()->name]);
                return redirect()->route('dashboard')->with('success', 'Login successful!');
            } else {
                // Multiple branches - redirect to branch selection
                return redirect()->route('branch.select')->with('success', 'Please select a branch to continue.');
            }
        }

        // Super Admin - no branch selection needed
        return redirect()->route('dashboard')->with('success', 'Login successful!');
    }
}
