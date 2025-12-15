<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp',
        'type',
        'is_verified',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the OTP verification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is valid.
     */
    public function isValid(): bool
    {
        return !$this->is_verified && !$this->isExpired();
    }
}
