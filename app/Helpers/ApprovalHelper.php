<?php

namespace App\Helpers;

use App\Models\ApprovalMaster;

class ApprovalHelper
{
    /**
     * Get approved records for a given form
     * This can be used in any form that depends on another form's approval
     */
    public static function getApprovedRecords(string $formName, $modelClass)
    {
        // Check if approval is required for this form
        $approvalMaster = ApprovalMaster::where('form_name', $formName)
            ->where('is_active', true)
            ->first();

        if (!$approvalMaster) {
            // No approval required, return all records
            return $modelClass::query();
        }

        // Return only approved records
        return $modelClass::approved();
    }

    /**
     * Check if a record is approved and can be used in dependent forms
     */
    public static function canUseInDependentForms($record): bool
    {
        // If the model doesn't have approval_status, allow it
        if (!isset($record->approval_status)) {
            return true;
        }

        return $record->approval_status === 'approved';
    }
}

