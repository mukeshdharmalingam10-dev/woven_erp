<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesInvoice::with('customer')->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $salesInvoices = $query->paginate(15)->withQueryString();

        return view('transactions.sales-invoices.index', compact('salesInvoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        return view('transactions.sales-invoices.create', compact('customers', 'products', 'companyInfo'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $invoiceNumber = $data['invoice_number'] ?? ('INV-' . strtoupper(Str::random(8)));

        $customer = Customer::find($data['customer_id']);
        
        // Format addresses from individual fields or use customer addresses
        $billingAddress = $this->formatAddressFromFields($request, 'billing');
        if (!$billingAddress && $customer) {
            $billingAddress = $this->formatBillingAddress($customer);
        }
        
        $shippingAddress = $this->formatAddressFromFields($request, 'shipping');
        if (!$shippingAddress && $customer) {
            $shippingAddress = $this->formatShippingAddress($customer);
        }

        $salesInvoice = new SalesInvoice();
        $salesInvoice->fill([
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $data['invoice_date'],
            'customer_id' => $data['customer_id'],
            'mode_of_order' => $data['mode_of_order'] ?? 'IMMEDIATE',
            'buyer_order_number' => $data['buyer_order_number'] ?? null,
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
            'gst_percentage_overall' => $data['gst_percentage_overall'] ?? 18,
            'gst_classification' => $data['gst_classification'] ?? $this->determineGstClassification($data['customer_id']),
        ]);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? [], $data['gst_percentage_overall'] ?? null);
        $salesInvoice->total_sales_amount = $totals['total_sales_amount'];
        $salesInvoice->total_gst_amount = $totals['total_gst_amount'];
        $salesInvoice->grand_total = $totals['grand_total'];

        $user = Auth::user();
        if ($user) {
            $salesInvoice->organization_id = $user->organization_id ?? null;
            $salesInvoice->branch_id = session('active_branch_id');
            $salesInvoice->created_by = $user->id;
        }

        $salesInvoice->save();

        foreach ($data['items'] as $item) {
            $totalAmount = (float) $item['quantity_sold'] * (float) $item['unit_price'];

            SalesInvoiceItem::create([
                'sales_invoice_id' => $salesInvoice->id,
                'product_id' => $item['product_id'],
                'description' => $item['description'] ?? null,
                'quantity_sold' => $item['quantity_sold'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $totalAmount,
                'gst_percentage' => null,
                'gst_amount' => 0,
                'line_total' => $totalAmount,
            ]);
        }

        // Check if print is requested
        if ($request->has('print')) {
            return redirect()->route('sales-invoices.index', ['print_id' => $salesInvoice->id])
                ->with('success', 'Sales Invoice created and saved successfully.');
        }

        return redirect()->route('sales-invoices.index')
            ->with('success', 'Sales Invoice created successfully.');
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'items.product']);
        return view('transactions.sales-invoices.show', compact('salesInvoice'));
    }

    public function edit(SalesInvoice $salesInvoice)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        $salesInvoice->load(['items', 'customer']);

        return view('transactions.sales-invoices.edit', compact('salesInvoice', 'customers', 'products', 'companyInfo'));
    }

    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        $data = $this->validateRequest($request, $salesInvoice);

        $customer = Customer::find($data['customer_id']);
        
        // Format addresses from individual fields or use customer addresses
        $billingAddress = $this->formatAddressFromFields($request, 'billing');
        if (!$billingAddress && $customer) {
            $billingAddress = $this->formatBillingAddress($customer);
        }
        
        $shippingAddress = $this->formatAddressFromFields($request, 'shipping');
        if (!$shippingAddress && $customer) {
            $shippingAddress = $this->formatShippingAddress($customer);
        }

        $salesInvoice->invoice_number = $data['invoice_number'] ?? $salesInvoice->invoice_number;
        $salesInvoice->invoice_date = $data['invoice_date'];
        $salesInvoice->customer_id = $data['customer_id'];
        $salesInvoice->mode_of_order = $data['mode_of_order'] ?? 'IMMEDIATE';
        $salesInvoice->buyer_order_number = $data['buyer_order_number'] ?? null;
        $salesInvoice->billing_address = $billingAddress;
        $salesInvoice->shipping_address = $shippingAddress;
        $salesInvoice->gst_percentage_overall = $data['gst_percentage_overall'] ?? 18;
        $salesInvoice->gst_classification = $data['gst_classification'] ?? $this->determineGstClassification($data['customer_id']);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? [], $data['gst_percentage_overall'] ?? null);
        $salesInvoice->total_sales_amount = $totals['total_sales_amount'];
        $salesInvoice->total_gst_amount = $totals['total_gst_amount'];
        $salesInvoice->grand_total = $totals['grand_total'];

        $salesInvoice->save();

        $salesInvoice->items()->delete();

        foreach ($data['items'] as $item) {
            $totalAmount = (float) $item['quantity_sold'] * (float) $item['unit_price'];

            SalesInvoiceItem::create([
                'sales_invoice_id' => $salesInvoice->id,
                'product_id' => $item['product_id'],
                'description' => $item['description'] ?? null,
                'quantity_sold' => $item['quantity_sold'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $totalAmount,
                'gst_percentage' => null,
                'gst_amount' => 0,
                'line_total' => $totalAmount,
            ]);
        }

        // Check if print is requested
        if ($request->has('print')) {
            return redirect()->route('sales-invoices.index', ['print_id' => $salesInvoice->id])
                ->with('success', 'Sales Invoice updated and saved successfully.');
        }

        return redirect()->route('sales-invoices.index')
            ->with('success', 'Sales Invoice updated successfully.');
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        $salesInvoice->delete();

        return redirect()->route('sales-invoices.index')
            ->with('success', 'Sales Invoice deleted successfully.');
    }

    protected function validateRequest(Request $request, ?SalesInvoice $salesInvoice = null): array
    {
        $rules = [
            'invoice_date' => ['required', 'date'],
            'customer_id' => ['required', 'exists:customers,id'],
            'mode_of_order' => ['nullable', 'string', 'max:191'],
            'buyer_order_number' => ['nullable', 'string', 'max:191'],
            'gst_percentage_overall' => ['nullable', 'numeric', 'min:0'],
            'gst_classification' => ['nullable', 'in:CGST_SGST,IGST'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity_sold' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];

        if (!$salesInvoice) {
            $rules['invoice_number'] = ['nullable', 'string', 'max:191', 'unique:sales_invoices,invoice_number'];
        } else {
            $rules['invoice_number'] = ['nullable', 'string', 'max:191', 'unique:sales_invoices,invoice_number,' . $salesInvoice->id];
        }

        return $request->validate($rules);
    }

    protected function calculateItemTotals($quantity, $unitPrice, $gstPercentage): array
    {
        $quantity = (float) $quantity;
        $unitPrice = (float) $unitPrice;
        $gstPercentage = $gstPercentage !== null ? (float) $gstPercentage : 0.0;

        $totalAmount = $quantity * $unitPrice;
        $gstAmount = $gstPercentage > 0 ? ($totalAmount * $gstPercentage) / 100 : 0;
        $lineTotal = $totalAmount + $gstAmount;

        return [
            'total_amount' => $totalAmount,
            'gst_amount' => $gstAmount,
            'line_total' => $lineTotal,
        ];
    }

    protected function calculateTotalsFromItems(array $items, ?float $gstPercentage = null): array
    {
        $totalSales = 0;

        foreach ($items as $item) {
            $totalSales += (float) $item['quantity_sold'] * (float) $item['unit_price'];
        }

        $totalGst = $gstPercentage && $gstPercentage > 0 ? ($totalSales * $gstPercentage) / 100 : 0;

        return [
            'total_sales_amount' => $totalSales,
            'total_gst_amount' => $totalGst,
            'grand_total' => $totalSales + $totalGst,
        ];
    }

    protected function determineGstClassification(int $customerId): ?string
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return null;
        }

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        if (!$companyInfo || !$companyInfo->state || !$customer->billing_state) {
            return null;
        }

        return strtolower($companyInfo->state) === strtolower($customer->billing_state)
            ? 'CGST_SGST'
            : 'IGST';
    }

    protected function formatBillingAddress(Customer $customer): string
    {
        $parts = [];
        if ($customer->billing_address_line_1) $parts[] = $customer->billing_address_line_1;
        if ($customer->billing_address_line_2) $parts[] = $customer->billing_address_line_2;
        if ($customer->billing_city) $parts[] = $customer->billing_city;
        if ($customer->billing_state) $parts[] = $customer->billing_state;
        if ($customer->billing_postal_code) $parts[] = $customer->billing_postal_code;
        if ($customer->billing_country) $parts[] = $customer->billing_country;
        return implode(', ', $parts);
    }

    protected function formatShippingAddress(Customer $customer): string
    {
        $parts = [];
        if ($customer->shipping_address_line_1) $parts[] = $customer->shipping_address_line_1;
        if ($customer->shipping_address_line_2) $parts[] = $customer->shipping_address_line_2;
        if ($customer->shipping_city) $parts[] = $customer->shipping_city;
        if ($customer->shipping_state) $parts[] = $customer->shipping_state;
        if ($customer->shipping_postal_code) $parts[] = $customer->shipping_postal_code;
        if ($customer->shipping_country) $parts[] = $customer->shipping_country;
        return implode(', ', $parts);
    }

    protected function formatAddressFromFields(Request $request, string $type): ?string
    {
        $prefix = $type . '_';
        $parts = [];
        
        if ($request->has($prefix . 'address_line_1') && $request->input($prefix . 'address_line_1')) {
            $parts[] = $request->input($prefix . 'address_line_1');
        }
        if ($request->has($prefix . 'address_line_2') && $request->input($prefix . 'address_line_2')) {
            $parts[] = $request->input($prefix . 'address_line_2');
        }
        if ($request->has($prefix . 'city') && $request->input($prefix . 'city')) {
            $parts[] = $request->input($prefix . 'city');
        }
        if ($request->has($prefix . 'state') && $request->input($prefix . 'state')) {
            $parts[] = $request->input($prefix . 'state');
        }
        if ($request->has($prefix . 'postal_code') && $request->input($prefix . 'postal_code')) {
            $parts[] = $request->input($prefix . 'postal_code');
        }
        if ($request->has($prefix . 'country') && $request->input($prefix . 'country')) {
            $parts[] = $request->input($prefix . 'country');
        }
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    public function print(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'items.product']);
        
        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        return view('transactions.sales-invoices.export-pdf', compact('salesInvoice', 'companyInfo'));
    }
}

