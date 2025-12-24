<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        $query = Customer::query();
        
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
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_name_1', 'like', "%{$search}%")
                  ->orWhere('contact_name_2', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('ifsc_code', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('bank_branch_name', 'like', "%{$search}%")
                  ->orWhere('billing_address_line_1', 'like', "%{$search}%")
                  ->orWhere('billing_city', 'like', "%{$search}%")
                  ->orWhere('billing_state', 'like', "%{$search}%")
                  ->orWhere('billing_postal_code', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        
        switch ($sortBy) {
            case 'customer_name':
                $query->orderBy('customer_name', $sortOrder);
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
        
        $customers = $query->paginate(15)->withQueryString();
        
        return view('masters.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('masters.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_name_1' => 'nullable|string|max:255',
            'contact_name_2' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gst_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'bank_branch_name' => 'nullable|string|max:255',
        ], [
            'customer_name.required' => 'Customer/Company Name is required.',
            'customer_name.max' => 'Customer/Company Name must not exceed 255 characters.',
            'contact_name_1.max' => 'Contact Name 1 must not exceed 255 characters.',
            'contact_name_2.max' => 'Contact Name 2 must not exceed 255 characters.',
            'phone_number.max' => 'Phone Number must not exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
            'bank_name.max' => 'Bank Name must not exceed 255 characters.',
            'ifsc_code.max' => 'IFSC Code must not exceed 50 characters.',
            'account_number.max' => 'Account Number must not exceed 50 characters.',
            'bank_branch_name.max' => 'Branch Name must not exceed 255 characters.',
            'billing_state.required' => 'State is required.',
            'billing_state.max' => 'State must not exceed 100 characters.',
        ]);

        // Generate sequential Customer ID starting from CUS001
        $allCustomers = Customer::withTrashed()
            ->where('code', 'like', 'CUS%')
            ->get();

        $maxNumber = 0;
        foreach ($allCustomers as $customer) {
            // Extract number from code (e.g., CUS001 -> 1, CUS123 -> 123)
            if (preg_match('/^CUS(\d+)$/i', $customer->code, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Next number is max + 1, starting from 1 if no customers exist
        $nextNumber = $maxNumber + 1;

        // Format as CUS001, CUS002, etc. (3 digits with leading zeros)
        $code = 'CUS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Safety check: if code exists (shouldn't happen, but just in case), find next available
        $maxAttempts = 10000;
        $attempts = 0;
        while (Customer::withTrashed()->where('code', $code)->exists() && $attempts < $maxAttempts) {
            $nextNumber++;
            $code = 'CUS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }

        $user = auth()->user();
        
        $customer = Customer::create([
            'customer_name' => $request->customer_name,
            'code' => $code,
            'contact_name_1' => $request->contact_name_1,
            'contact_name_2' => $request->contact_name_2,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'gst_number' => $request->gst_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'account_number' => $request->account_number,
            'bank_branch_name' => $request->bank_branch_name,
            'billing_address_line_1' => $request->billing_address_line_1,
            'billing_address_line_2' => $request->billing_address_line_2,
            'billing_city' => $request->billing_city,
            'billing_state' => $request->billing_state,
            'billing_postal_code' => $request->billing_postal_code,
            'billing_country' => $request->billing_country ?? 'India',
            'shipping_address_line_1' => $request->shipping_address_line_1,
            'shipping_address_line_2' => $request->shipping_address_line_2,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_country' => $request->shipping_country ?? 'India',
            'organization_id' => $user->organization_id,
            'branch_id' => $user->branch_id,
            'created_by' => $user->id,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        return view('masters.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        return view('masters.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_name_1' => 'nullable|string|max:255',
            'contact_name_2' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gst_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'bank_branch_name' => 'nullable|string|max:255',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_postal_code' => 'nullable|string|max:10',
            'billing_country' => 'nullable|string|max:100',
            'shipping_address_line_1' => 'nullable|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10',
            'shipping_country' => 'nullable|string|max:100',
        ], [
            'customer_name.required' => 'Customer/Company Name is required.',
            'customer_name.max' => 'Customer/Company Name must not exceed 255 characters.',
            'contact_name_1.max' => 'Contact Name 1 must not exceed 255 characters.',
            'contact_name_2.max' => 'Contact Name 2 must not exceed 255 characters.',
            'phone_number.max' => 'Phone Number must not exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
            'bank_name.max' => 'Bank Name must not exceed 255 characters.',
            'ifsc_code.max' => 'IFSC Code must not exceed 50 characters.',
            'account_number.max' => 'Account Number must not exceed 50 characters.',
            'bank_branch_name.max' => 'Branch Name must not exceed 255 characters.',
            'billing_state.required' => 'State is required.',
            'billing_state.max' => 'State must not exceed 100 characters.',
        ]);

        // Customer ID (code) is not editable - it remains the same
        $customer->update([
            'customer_name' => $request->customer_name,
            'contact_name_1' => $request->contact_name_1,
            'contact_name_2' => $request->contact_name_2,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'gst_number' => $request->gst_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'account_number' => $request->account_number,
            'bank_branch_name' => $request->bank_branch_name,
            'billing_address_line_1' => $request->billing_address_line_1,
            'billing_address_line_2' => $request->billing_address_line_2,
            'billing_city' => $request->billing_city,
            'billing_state' => $request->billing_state,
            'billing_postal_code' => $request->billing_postal_code,
            'billing_country' => $request->billing_country ?? 'India',
            'shipping_address_line_1' => $request->shipping_address_line_1,
            'shipping_address_line_2' => $request->shipping_address_line_2,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_country' => $request->shipping_country ?? 'India',
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
