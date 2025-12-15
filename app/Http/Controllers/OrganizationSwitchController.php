<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrganizationSwitchController extends Controller
{
    /**
     * Switch to a specific organization for reporting/oversight
     */
    public function switch(Request $request, int $organizationId): RedirectResponse
    {
        $user = auth()->user();
        
        // Only Super Admin can switch organizations
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can switch organizations.');
        }
        
        $organization = Organization::findOrFail($organizationId);
        
        // Store selected organization in session
        session(['viewing_organization_id' => $organization->id]);
        
        return redirect()->back()->with('success', "Switched to organization: {$organization->name}");
    }
    
    /**
     * Clear organization view (return to all organizations view)
     */
    public function clear(): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can clear organization view.');
        }
        
        session()->forget('viewing_organization_id');
        
        return redirect()->back()->with('success', 'Viewing all organizations');
    }
}

