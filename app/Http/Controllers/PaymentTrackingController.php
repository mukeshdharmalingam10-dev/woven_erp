<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\PaymentTracking;
use App\Models\Customer;
use App\Models\SalesInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PaymentTrackingController extends Controller
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
        $this->checkReadPermission('payment-trackings');
        
        $user = auth()->user();
        $query = PaymentTracking::with(['customer', 'salesInvoice'])->orderByDesc('id');
        
        // Filter by organization/branch if needed
        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        $branchId = session('active_branch_id');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($qc) use ($search) {
                    $qc->where('customer_name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhereHas('salesInvoice', function($qi) use ($search) {
                    $qi->where('invoice_number', 'like', "%{$search}%");
                })
                ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }
        
        $paymentTrackings = $query->paginate(15)->withQueryString();
        
        // Calculate invoice totals, total paid, and balance for each payment
        $paymentTrackings->getCollection()->transform(function($payment) {
            $invoice = $payment->salesInvoice;
            if ($invoice) {
                $totalPaid = PaymentTracking::where('sales_invoice_id', $invoice->id)
                    ->sum('payment_amount');
                $balance = $invoice->grand_total - $totalPaid;
                
                $payment->invoice_total = $invoice->grand_total;
                $payment->total_paid = $totalPaid;
                $payment->balance = $balance;
            } else {
                $payment->invoice_total = 0;
                $payment->total_paid = 0;
                $payment->balance = 0;
            }
            return $payment;
        });
        
        $permissions = $this->getPermissionFlags('payment-trackings');
        
        return view('transactions.payment-trackings.index', compact('paymentTrackings') + $permissions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->checkWritePermission('payment-trackings');
        
        // Get only customers who have unpaid or partially paid invoices
        $customers = Customer::whereHas('salesInvoices', function($query) {
            $query->whereRaw('(
                SELECT COALESCE(SUM(payment_amount), 0)
                FROM payment_trackings
                WHERE payment_trackings.sales_invoice_id = sales_invoices.id
                AND payment_trackings.deleted_at IS NULL
            ) < sales_invoices.grand_total');
        })->orderBy('customer_name')->get();
        
        return view('transactions.payment-trackings.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkWritePermission('payment-trackings');
        
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Cheque,Bank Transfer,UPI,Credit Card,Debit Card,Other',
            'remarks' => 'nullable|string|max:1000',
        ], [
            'customer_id.required' => 'Customer is required.',
            'sales_invoice_id.required' => 'Invoice Number is required.',
            'payment_date.required' => 'Payment Date is required.',
            'payment_date.before_or_equal' => 'Payment Date must be today or a past date.',
            'payment_amount.required' => 'Payment Amount is required.',
            'payment_amount.min' => 'Payment Amount must be greater than 0.',
            'payment_method.required' => 'Payment Method is required.',
        ]);

        // Validate payment amount doesn't exceed invoice balance
        $invoice = SalesInvoice::findOrFail($request->sales_invoice_id);
        $totalPaid = PaymentTracking::where('sales_invoice_id', $invoice->id)->sum('payment_amount');
        $balance = $invoice->grand_total - $totalPaid;
        
        if ($request->payment_amount > $balance) {
            return back()->withErrors(['payment_amount' => 'Payment amount cannot exceed the invoice balance of ' . number_format($balance, 2) . '.'])->withInput();
        }

        $user = Auth::user();
        
        PaymentTracking::create([
            'customer_id' => $request->customer_id,
            'sales_invoice_id' => $request->sales_invoice_id,
            'payment_date' => $request->payment_date,
            'payment_amount' => $request->payment_amount,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
            'organization_id' => $user->organization_id,
            'branch_id' => session('active_branch_id'),
            'created_by' => $user->id,
        ]);

        return redirect()->route('payment-trackings.index')
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentTracking $paymentTracking): View
    {
        $this->checkReadPermission('payment-trackings');
        $permissions = $this->getPermissionFlags('payment-trackings');
        
        $paymentTracking->load(['customer', 'salesInvoice', 'creator']);
        
        return view('transactions.payment-trackings.show', compact('paymentTracking') + $permissions);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentTracking $paymentTracking): View
    {
        $this->checkWritePermission('payment-trackings');
        
        // Get customers who have unpaid or partially paid invoices
        // Also include the current customer (even if all invoices are paid) for edit mode
        $customers = Customer::where(function($query) use ($paymentTracking) {
            $query->whereHas('salesInvoices', function($q) {
                $q->whereRaw('(
                    SELECT COALESCE(SUM(payment_amount), 0)
                    FROM payment_trackings
                    WHERE payment_trackings.sales_invoice_id = sales_invoices.id
                    AND payment_trackings.deleted_at IS NULL
                ) < sales_invoices.grand_total');
            })
            ->orWhere('id', $paymentTracking->customer_id); // Include current customer
        })->orderBy('customer_name')->get();
        
        $paymentTracking->load('salesInvoice');
        
        return view('transactions.payment-trackings.edit', compact('paymentTracking', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentTracking $paymentTracking): RedirectResponse
    {
        $this->checkWritePermission('payment-trackings');
        
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Cheque,Bank Transfer,UPI,Credit Card,Debit Card,Other',
            'remarks' => 'nullable|string|max:1000',
        ], [
            'customer_id.required' => 'Customer is required.',
            'sales_invoice_id.required' => 'Invoice Number is required.',
            'payment_date.required' => 'Payment Date is required.',
            'payment_date.before_or_equal' => 'Payment Date must be today or a past date.',
            'payment_amount.required' => 'Payment Amount is required.',
            'payment_amount.min' => 'Payment Amount must be greater than 0.',
            'payment_method.required' => 'Payment Method is required.',
        ]);

        // Validate payment amount doesn't exceed invoice balance (excluding current payment)
        $invoice = SalesInvoice::findOrFail($request->sales_invoice_id);
        $totalPaid = PaymentTracking::where('sales_invoice_id', $invoice->id)
            ->where('id', '!=', $paymentTracking->id)
            ->sum('payment_amount');
        $balance = $invoice->grand_total - $totalPaid;
        
        if ($request->payment_amount > $balance) {
            return back()->withErrors(['payment_amount' => 'Payment amount cannot exceed the invoice balance of ' . number_format($balance, 2) . '.'])->withInput();
        }

        $paymentTracking->update([
            'customer_id' => $request->customer_id,
            'sales_invoice_id' => $request->sales_invoice_id,
            'payment_date' => $request->payment_date,
            'payment_amount' => $request->payment_amount,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('payment-trackings.index')
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentTracking $paymentTracking): RedirectResponse
    {
        $this->checkDeletePermission('payment-trackings');
        
        $paymentTracking->delete();

        return redirect()->route('payment-trackings.index')
            ->with('success', 'Payment deleted successfully.');
    }

    /**
     * Get unpaid/partially paid invoices for a customer (AJAX)
     */
    public function getInvoices(Request $request)
    {
        try {
            $customerId = $request->get('customer_id');
            $currentInvoiceId = $request->get('current_invoice_id'); // For edit mode
            
            if (!$customerId) {
                return response()->json([]);
            }

            $invoices = SalesInvoice::where('customer_id', $customerId)
                ->with('customer')
                ->get()
                ->map(function($invoice) {
                    $totalPaid = PaymentTracking::where('sales_invoice_id', $invoice->id)->sum('payment_amount');
                    $balance = $invoice->grand_total - $totalPaid;
                    
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'invoice_date' => $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '',
                        'grand_total' => number_format($invoice->grand_total, 2),
                        'total_paid' => number_format($totalPaid, 2),
                        'balance' => number_format($balance, 2),
                        'balance_raw' => $balance,
                    ];
                })
                ->filter(function($invoice) use ($currentInvoiceId) {
                    // Include invoices with balance > 0 OR the current invoice (for edit mode)
                    return $invoice['balance_raw'] > 0 || ($currentInvoiceId && $invoice['id'] == $currentInvoiceId);
                })
                ->values();

            return response()->json($invoices);
        } catch (\Exception $e) {
            \Log::error('Error fetching invoices for customer ' . $customerId . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error loading invoices: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get transaction history for a specific invoice (AJAX)
     */
    public function getTransactionHistory($invoiceId)
    {
        try {
            $invoice = SalesInvoice::findOrFail($invoiceId);
            
            $transactions = PaymentTracking::where('sales_invoice_id', $invoiceId)
                ->orderBy('payment_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'payment_date' => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '',
                        'payment_date_formatted' => $payment->payment_date ? $payment->payment_date->format('d M Y') : '',
                        'payment_amount' => number_format($payment->payment_amount, 2),
                        'payment_amount_raw' => $payment->payment_amount,
                    ];
                });

            return response()->json([
                'invoice_number' => $invoice->invoice_number,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching transaction history for invoice ' . $invoiceId . ': ' . $e->getMessage());
            return response()->json(['error' => 'Error loading transaction history: ' . $e->getMessage()], 500);
        }
    }
}
