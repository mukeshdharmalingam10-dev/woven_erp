<?php

namespace App\Http\Controllers;

use App\Models\CompanyInformation;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Helpers\FileUploadHelper;

class CompanyInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Only Super Admin can access
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access company information.');
        }

        $companyInfos = CompanyInformation::with('branch')->latest()->paginate(15);
        return view('settings.company-information.index', compact('companyInfos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Only Super Admin can access
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create company information.');
        }

        $branches = Branch::where('is_active', true)->get();
        $branchesWithInfo = CompanyInformation::pluck('branch_id')->toArray();
        $availableBranches = $branches->filter(function($branch) use ($branchesWithInfo) {
            return !in_array($branch->id, $branchesWithInfo);
        });

        return view('settings.company-information.create', compact('availableBranches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Only Super Admin can access
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create company information.');
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id|unique:company_information,branch_id',
            'company_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10|regex:/^[0-9]{6}$/',
            'gstin' => [
                'required',
                'string',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                'unique:company_information,gstin'
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'gstin.regex' => 'The GSTIN format is invalid. Format: 15 characters (2 digits + 5 letters + 4 digits + 1 letter + 1 alphanumeric + Z + 1 alphanumeric)',
            'pincode.regex' => 'The pincode must be 6 digits.',
        ]);

        $data = $request->only([
            'branch_id', 'company_name', 'address_line_1', 'address_line_2',
            'city', 'state', 'pincode', 'gstin', 'email', 'phone'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = FileUploadHelper::storeWithOriginalName(
                $request->file('logo'),
                'company-logos'
            );
            $data['logo_path'] = $logoPath;
        }

        CompanyInformation::create($data);

        return redirect()->route('company-information.index')
            ->with('success', 'Company information created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        // Only Super Admin can access
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can view company information.');
        }

        $companyInfo = CompanyInformation::with('branch')->findOrFail($id);
        return view('settings.company-information.show', compact('companyInfo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Only Super Admin can access
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can edit company information.');
        }

        $companyInfo = CompanyInformation::with('branch')->findOrFail($id);
        return view('settings.company-information.edit', compact('companyInfo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Only Super Admin can access
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update company information.');
        }

        $companyInfo = CompanyInformation::findOrFail($id);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10|regex:/^[0-9]{6}$/',
            'gstin' => [
                'required',
                'string',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                Rule::unique('company_information', 'gstin')->ignore($id)
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'gstin.regex' => 'The GSTIN format is invalid. Format: 15 characters (2 digits + 5 letters + 4 digits + 1 letter + 1 alphanumeric + Z + 1 alphanumeric)',
            'pincode.regex' => 'The pincode must be 6 digits.',
        ]);

        $data = $request->only([
            'company_name', 'address_line_1', 'address_line_2',
            'city', 'state', 'pincode', 'gstin', 'email', 'phone'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($companyInfo->logo_path && Storage::disk('public')->exists($companyInfo->logo_path)) {
                Storage::disk('public')->delete($companyInfo->logo_path);
            }
            
            $logoPath = FileUploadHelper::storeWithOriginalName(
                $request->file('logo'),
                'company-logos'
            );
            $data['logo_path'] = $logoPath;
        }

        $companyInfo->update($data);

        return redirect()->route('company-information.index')
            ->with('success', 'Company information updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Only Super Admin can access
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can delete company information.');
        }

        $companyInfo = CompanyInformation::findOrFail($id);
        
        // Delete logo if exists
        if ($companyInfo->logo_path && Storage::disk('public')->exists($companyInfo->logo_path)) {
            Storage::disk('public')->delete($companyInfo->logo_path);
        }
        
        $companyInfo->delete();

        return redirect()->route('company-information.index')
            ->with('success', 'Company information deleted successfully.');
    }
}
