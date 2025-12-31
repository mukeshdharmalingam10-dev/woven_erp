<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->checkReadPermission('suppliers');
        $user = auth()->user();
        
        $query = Supplier::query();
        
        // Filter by organization/branch if needed
        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        if ($user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        
        switch ($sortBy) {
            case 'supplier_name':
                $query->orderBy('supplier_name', $sortOrder);
                break;
            case 'code':
                $query->orderBy('code', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            default:
                $query->orderBy('id', $sortOrder);
                break;
        }
        
        $suppliers = $query->paginate(15)->withQueryString();
        
        // Pass permission flags to view
        $permissions = $this->getPermissionFlags('suppliers');
        
        return view('masters.suppliers.index', compact('suppliers') + $permissions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->checkWritePermission('suppliers');
        return view('masters.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkWritePermission('suppliers');
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]{0,10}$/|max:10',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
        ], [
            'supplier_name.required' => 'Supplier Name is required.',
            'supplier_name.max' => 'Supplier Name must not exceed 255 characters.',
            'contact_name.max' => 'Contact Name must not exceed 255 characters.',
            'phone_number.regex' => 'Phone number must contain only numbers.',
            'phone_number.max' => 'Phone number must not exceed 10 digits.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'address_line_1.max' => 'Address Line 1 must not exceed 255 characters.',
            'address_line_2.max' => 'Address Line 2 must not exceed 255 characters.',
            'city.max' => 'City must not exceed 100 characters.',
            'state.required' => 'State is required.',
            'state.max' => 'State must not exceed 100 characters.',
            'postal_code.max' => 'Postal Code must not exceed 20 characters.',
            'country.max' => 'Country must not exceed 100 characters.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
        ]);

        // Generate sequential Supplier ID starting from SUP001
        // Find the highest existing SUP### number
        $allSuppliers = Supplier::withTrashed()
            ->where('code', 'like', 'SUP%')
            ->get();

        $maxNumber = 0;
        foreach ($allSuppliers as $supplier) {
            // Extract number from code (e.g., SUP001 -> 1, SUP123 -> 123)
            if (preg_match('/^SUP(\d+)$/i', $supplier->code, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Next number is max + 1, starting from 1 if no suppliers exist
        $nextNumber = $maxNumber + 1;

        // Format as SUP001, SUP002, etc. (3 digits with leading zeros)
        $code = 'SUP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Safety check: if code exists (shouldn't happen, but just in case), find next available
        $maxAttempts = 10000;
        $attempts = 0;
        while (Supplier::withTrashed()->where('code', $code)->exists() && $attempts < $maxAttempts) {
            $nextNumber++;
            $code = 'SUP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }

        $user = auth()->user();
        
        $supplier = Supplier::create([
            'supplier_name' => $request->supplier_name,
            'code' => $code,
            'contact_name' => $request->contact_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'gst_number' => $request->gst_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'account_number' => $request->account_number,
            'branch_name' => $request->branch_name,
            'organization_id' => $user->organization_id,
            'branch_id' => $user->branch_id,
            'created_by' => $user->id,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): View
    {
        $this->checkReadPermission('suppliers');
        $permissions = $this->getPermissionFlags('suppliers');
        return view('masters.suppliers.show', compact('supplier') + $permissions);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        $this->checkWritePermission('suppliers');
        return view('masters.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->checkWritePermission('suppliers');
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]{0,10}$/|max:10',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:11',
            'account_number' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:255',
        ], [
            'supplier_name.required' => 'Supplier Name is required.',
            'supplier_name.max' => 'Supplier Name must not exceed 255 characters.',
            'contact_name.max' => 'Contact Name must not exceed 255 characters.',
            'phone_number.regex' => 'Phone number must contain only numbers.',
            'phone_number.max' => 'Phone number must not exceed 10 digits.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'address_line_1.max' => 'Address Line 1 must not exceed 255 characters.',
            'address_line_2.max' => 'Address Line 2 must not exceed 255 characters.',
            'city.max' => 'City must not exceed 100 characters.',
            'state.required' => 'State is required.',
            'state.max' => 'State must not exceed 100 characters.',
            'postal_code.max' => 'Postal Code must not exceed 20 characters.',
            'country.max' => 'Country must not exceed 100 characters.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
            'bank_name.max' => 'Bank Name must not exceed 255 characters.',
            'ifsc_code.max' => 'IFSC Code must not exceed 11 characters.',
            'account_number.max' => 'Account Number must not exceed 50 characters.',
            'branch_name.max' => 'Branch Name must not exceed 255 characters.',
        ]);

        // Supplier ID (code) is not editable - it remains the same
        $supplier->update([
            'supplier_name' => $request->supplier_name,
            'contact_name' => $request->contact_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'gst_number' => $request->gst_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'account_number' => $request->account_number,
            'branch_name' => $request->branch_name,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->checkDeletePermission('suppliers');
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
