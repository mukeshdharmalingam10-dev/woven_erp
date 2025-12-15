<?php

namespace App\Services\Auth;

use App\Models\OtpVerification;
use App\Models\User;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generate and store OTP for user
     *
     * @param User $user
     * @param string $type
     * @return string
     */
    public function generateOtp(User $user, string $type = 'login'): string
    {
        // Invalidate previous OTPs
        OtpVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_verified', false)
            ->update(['is_verified' => true]);

        // Generate 6-digit OTP
        $otp = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP
        OtpVerification::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(10), // OTP expires in 10 minutes
        ]);

        return $otp;
    }

    /**
     * Verify OTP
     *
     * @param User $user
     * @param string $otp
     * @param string $type
     * @return bool
     */
    public function verifyOtp(User $user, string $otp, string $type = 'login'): bool
    {
        $otpVerification = OtpVerification::where('user_id', $user->id)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('is_verified', false)
            ->latest()
            ->first();

        if (!$otpVerification) {
            return false;
        }

        if ($otpVerification->isExpired()) {
            return false;
        }

        // Mark as verified
        $otpVerification->update([
            'is_verified' => true,
            'verified_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Get latest OTP for user (for auto-fill)
     *
     * @param User $user
     * @param string $type
     * @return string|null
     */
    public function getLatestOtp(User $user, string $type = 'login'): ?string
    {
        $otpVerification = OtpVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_verified', false)
            ->latest()
            ->first();

        if (!$otpVerification || $otpVerification->isExpired()) {
            return null;
        }

        return $otpVerification->otp;
    }
}

