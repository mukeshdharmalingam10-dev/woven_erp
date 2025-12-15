<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(): View
    {
        $user = auth()->user();
        
        // Only Super Admin can see all organizations
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        $organizations = Organization::with(['admin', 'branches'])->latest()->paginate(15);
        return view('organizations.index', compact('organizations'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Only Super Admin can create organizations
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Only Super Admin can create organizations
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'contact_info' => 'nullable|string|max:500',
            'organization_admin_email' => 'required|email|max:255|unique:users,email',
        ], [
            'organization_admin_email.unique' => 'A user with this email already exists.',
        ]);

        // Generate a default password
        $defaultPassword = 'OrgAdmin@' . date('Y');
        
        // Get Organization Admin role
        $orgAdminRole = Role::where('slug', 'organization-admin')->first();
        
        if (!$orgAdminRole) {
            return back()->withErrors(['organization_admin_email' => 'Organization Admin role not found. Please contact system administrator.'])->withInput();
        }

        // Generate unique organization code
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->name), 0, 10));
        $code = $baseCode;
        $counter = 1;
        while (Organization::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }

        // Create the organization
        $organization = Organization::create([
            'name' => $request->name,
            'code' => $code,
            'description' => $request->description,
            'address' => $request->contact_info,
        ]);

        // Create the Organization Admin user
        $adminUser = User::create([
            'name' => 'Organization Admin - ' . $organization->name,
            'email' => $request->organization_admin_email,
            'password' => Hash::make($defaultPassword),
            'role_id' => $orgAdminRole->id,
            'organization_id' => $organization->id,
            'mobile' => '0000000000',
        ]);

        // Update organization with admin_id
        $organization->update(['admin_id' => $adminUser->id]);

        return redirect()->route('organizations.index')
            ->with('success', 'Organization created successfully. Organization Admin user created with email: ' . $request->organization_admin_email . ' (Default password: ' . $defaultPassword . ')');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $organization = Organization::with(['admin', 'branches', 'users'])->findOrFail($id);
        return view('organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        // Only Super Admin can edit organizations
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        $organization = Organization::with('admin')->findOrFail($id);
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // Only Super Admin can update organizations
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $organization = Organization::with('admin')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'contact_info' => 'nullable|string|max:500',
            'organization_admin_email' => 'nullable|email|max:255',
        ]);

        // Update organization basic info
        $organization->update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->contact_info,
        ]);

        // Handle Organization Admin email change
        if ($request->filled('organization_admin_email')) {
            $newAdminEmail = $request->organization_admin_email;
            
            // Check if email is different from current admin
            if (!$organization->admin || $organization->admin->email !== $newAdminEmail) {
                // Check if user with this email exists
                $existingUser = User::where('email', $newAdminEmail)->first();
                
                if ($existingUser) {
                    // Update existing user to be Organization Admin
                    $orgAdminRole = Role::where('slug', 'organization-admin')->first();
                    if ($orgAdminRole) {
                        $existingUser->update([
                            'role_id' => $orgAdminRole->id,
                            'organization_id' => $organization->id,
                        ]);
                        $organization->update(['admin_id' => $existingUser->id]);
                    }
                } else {
                    // Create new Organization Admin user
                    $orgAdminRole = Role::where('slug', 'organization-admin')->first();
                    if ($orgAdminRole) {
                        $defaultPassword = 'OrgAdmin@' . date('Y');
                        
                        $adminUser = User::create([
                            'name' => 'Organization Admin - ' . $organization->name,
                            'email' => $newAdminEmail,
                            'password' => Hash::make($defaultPassword),
                            'role_id' => $orgAdminRole->id,
                            'organization_id' => $organization->id,
                            'mobile' => '0000000000',
                        ]);
                        
                        $organization->update(['admin_id' => $adminUser->id]);
                    }
                }
            }
        }

        return redirect()->route('organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }
}
