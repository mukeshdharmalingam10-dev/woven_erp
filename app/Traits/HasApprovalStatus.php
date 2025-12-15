<?php

namespace App\Traits;

trait HasApprovalStatus
{
    /**
     * Scope to get only approved records
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope to get only pending records
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope to get only rejected records
     */
    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    /**
     * Check if record is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if record is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if record is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }
}

