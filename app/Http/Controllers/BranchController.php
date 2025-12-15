<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // Super Admin can view all branches
            $branches = Branch::with('users')->latest()->paginate(15);
        } elseif ($user->isBranchUser()) {
            // Branch User can see all their assigned branches
            $userBranchIds = $user->branches->pluck('id')->toArray();
            if (empty($userBranchIds)) {
                $branches = Branch::whereRaw('1 = 0')->paginate(15);
            } else {
                $branches = Branch::whereIn('id', $userBranchIds)
                    ->with('users')
                    ->latest()
                    ->paginate(15);
            }
        } else {
            $branches = Branch::whereRaw('1 = 0')->paginate(15);
        }

        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Only Super Admin can create branches
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create branches.');
        }

        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Only Super Admin can create branches
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create branches.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10|regex:/^[0-9]{6}$/',
            'phone' => 'nullable|string|max:500',
        ], [
            'address_line_1.required' => 'Address Line 1 is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'pincode.required' => 'Pincode is required.',
            'pincode.regex' => 'The pincode must be 6 digits.',
        ]);

        // Generate unique code
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->name), 0, 10));
        $code = $baseCode;
        $counter = 1;
        while (Branch::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }

        Branch::create([
            'name' => $request->name,
            'code' => $code,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'phone' => $request->phone,
            'organization_id' => null, // No organization in simplified structure
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $user = auth()->user();
        $branch = Branch::with('users')->findOrFail($id);
        
        // Check access for Branch Users
        if ($user->isBranchUser() && !$user->hasAccessToBranch($branch->id)) {
            abort(403, 'You do not have access to this branch.');
        }

        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $branch = Branch::findOrFail($id);
        $user = auth()->user();
        
        // Only Super Admin can edit branches
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can edit branches.');
        }

        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $branch = Branch::findOrFail($id);
        $user = auth()->user();
        
        // Only Super Admin can update branches
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update branches.');
        }

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10|regex:/^[0-9]{6}$/',
            'phone' => 'nullable|string|max:500',
        ], [
            'address_line_1.required' => 'Address Line 1 is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'pincode.required' => 'Pincode is required.',
            'pincode.regex' => 'The pincode must be 6 digits.',
        ]);

        $branch->update([
            'name' => $request->name,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'phone' => $request->phone,
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $branch = Branch::findOrFail($id);
        $user = auth()->user();
        
        // Only Super Admin can delete branches
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can delete branches.');
        }

        // Prevent deleting the currently active branch (the one user is logged into)
        $activeBranchId = session('active_branch_id');
        if ($activeBranchId && (int)$activeBranchId === (int)$branch->id) {
            return redirect()->route('branches.index')
                ->with('error', 'You cannot delete the currently active branch. Please switch to a different branch first.');
        }

        // Prevent deleting a branch that still has users (including Admin/Super Admin) assigned
        if ($branch->users()->exists() || $branch->directUsers()->exists()) {
            return redirect()->route('branches.index')
                ->with('error', 'This branch has users (including Admin/Super Admin) assigned. Remove or reassign those users before deleting the branch.');
        }

        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
