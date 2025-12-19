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
                  ->orWhere('phone_number', 'like', "%{$search}%");
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
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'required|in:cash,credit,advance,partial,other',
            'gst_number' => 'nullable|string|max:50',
            'bank_details' => 'nullable|string',
        ], [
            'supplier_name.required' => 'Supplier Name is required.',
            'supplier_name.max' => 'Supplier Name must not exceed 255 characters.',
            'contact_name.max' => 'Contact Name must not exceed 255 characters.',
            'phone_number.max' => 'Phone Number must not exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'address_line_1.max' => 'Address Line 1 must not exceed 255 characters.',
            'address_line_2.max' => 'Address Line 2 must not exceed 255 characters.',
            'city.max' => 'City must not exceed 100 characters.',
            'state.max' => 'State must not exceed 100 characters.',
            'postal_code.max' => 'Postal Code must not exceed 20 characters.',
            'country.max' => 'Country must not exceed 100 characters.',
            'payment_terms.required' => 'Payment Terms is required.',
            'payment_terms.in' => 'Payment Terms must be one of: Cash, Credit, Advance, Partial, or Other.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
        ]);

        // Generate unique code
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->supplier_name), 0, 10));
        
        // Ensure base code is not empty
        if (empty($baseCode)) {
            $baseCode = 'SUP' . strtoupper(substr(md5($request->supplier_name), 0, 7));
        }
        
        $code = $baseCode;
        $counter = 1;
        $maxAttempts = 1000; // Safety limit
        
        // Check for existing code including soft-deleted records
        while (Supplier::withTrashed()->where('code', $code)->exists() && $counter < $maxAttempts) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }
        
        // If still not unique after max attempts, add timestamp
        if (Supplier::withTrashed()->where('code', $code)->exists()) {
            $code = $baseCode . '_' . time();
        }

        $user = auth()->user();
        
        try {
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
                'payment_terms' => $request->payment_terms,
                'gst_number' => $request->gst_number,
                'bank_details' => $request->bank_details,
                'is_active' => $request->has('is_active') ? true : false,
                'organization_id' => $user->organization_id,
                'branch_id' => $user->branch_id,
                'created_by' => $user->id,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // If still duplicate, generate with timestamp
            if ($e->getCode() == 23000) { // Integrity constraint violation
                $code = $baseCode . '_' . time() . '_' . rand(1000, 9999);
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
                    'payment_terms' => $request->payment_terms,
                    'gst_number' => $request->gst_number,
                    'bank_details' => $request->bank_details,
                    'is_active' => $request->has('is_active') ? true : false,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                    'created_by' => $user->id,
                ]);
            } else {
                throw $e;
            }
        }

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
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'required|in:cash,credit,advance,partial,other',
            'gst_number' => 'nullable|string|max:50',
            'bank_details' => 'nullable|string',
        ], [
            'supplier_name.required' => 'Supplier Name is required.',
            'supplier_name.max' => 'Supplier Name must not exceed 255 characters.',
            'contact_name.max' => 'Contact Name must not exceed 255 characters.',
            'phone_number.max' => 'Phone Number must not exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'address_line_1.max' => 'Address Line 1 must not exceed 255 characters.',
            'address_line_2.max' => 'Address Line 2 must not exceed 255 characters.',
            'city.max' => 'City must not exceed 100 characters.',
            'state.max' => 'State must not exceed 100 characters.',
            'postal_code.max' => 'Postal Code must not exceed 20 characters.',
            'country.max' => 'Country must not exceed 100 characters.',
            'payment_terms.required' => 'Payment Terms is required.',
            'payment_terms.in' => 'Payment Terms must be one of: Cash, Credit, Advance, Partial, or Other.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
        ]);

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
            'payment_terms' => $request->payment_terms,
            'gst_number' => $request->gst_number,
            'bank_details' => $request->bank_details,
            'is_active' => $request->has('is_active') ? true : false,
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
