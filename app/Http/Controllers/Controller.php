<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get the active branch ID from session.
     * Used for ALL authenticated users (including Admin / Super Admin).
     */
    protected function getActiveBranchId()
    {
        $user = auth()->user();
        
        if (!$user) {
            return null;
        }
        
        // Get active branch from session
        $activeBranchId = Session::get('active_branch_id');
        
        // If no active branch in session, try to get first branch for user
        if (!$activeBranchId && $user->branches()->where('is_active', true)->count() > 0) {
            $firstBranch = $user->branches()->where('is_active', true)->first();
            if ($firstBranch) {
                Session::put('active_branch_id', $firstBranch->id);
                Session::put('active_branch_name', $firstBranch->name);
                Session::save();
                return $firstBranch->id;
            }
        }
        
        return $activeBranchId;
    }

    /**
     * Apply branch filtering to a query builder.
     * For ALL authenticated users, filters by active branch if the model has branch_id.
     */
    protected function applyBranchFilter($query, $model = null)
    {
        $user = auth()->user();
        
        $activeBranchId = $this->getActiveBranchId();
        
        // If no active branch, return empty result for Branch Users
        if (!$activeBranchId) {
            // Return empty query result
            return $query->whereRaw('1 = 0');
        }
        
        // Get table name
        $tableName = null;
        if ($model) {
            if (is_string($model)) {
                // If it's a class name, instantiate it to get table name
                try {
                    $modelInstance = new $model;
                    $tableName = $modelInstance->getTable();
                } catch (\Exception $e) {
                    // If we can't instantiate, try to get from query
                    try {
                        $tableName = $query->getModel()->getTable();
                    } catch (\Exception $e2) {
                        return $query;
                    }
                }
            } else {
                $tableName = $model->getTable();
            }
        } else {
            // Try to get table name from query
            try {
                $tableName = $query->getModel()->getTable();
            } catch (\Exception $e) {
                // If we can't get the model, return query as-is
                return $query;
            }
        }
        
        // Check if table has branch_id column and filter by it
        if ($tableName && Schema::hasColumn($tableName, 'branch_id')) {
            // Only show records that match the active branch
            return $query->where($tableName . '.branch_id', $activeBranchId);
        }
        
        // If model doesn't have branch_id, return query as-is
        // (Some models like Units might be shared across branches)
        return $query;
    }
}
