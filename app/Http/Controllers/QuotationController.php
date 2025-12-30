<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('customer');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        switch ($sortBy) {
            case 'quotation_id':
                $query->orderBy('quotations.quotation_id', $sortOrder);
                break;
            case 'customer':
                $query->leftJoin('customers', 'quotations.customer_id', '=', 'customers.id')
                      ->orderBy('customers.customer_name', $sortOrder)
                      ->select('quotations.*');
                break;
            case 'company_name':
                $query->orderBy('quotations.company_name', $sortOrder);
                break;
            case 'total_amount':
                $query->orderBy('quotations.total_amount', $sortOrder);
                break;
            default:
                $query->orderBy('quotations.id', $sortOrder);
                break;
        }

        $quotations = $query->paginate(15)->withQueryString();

        return view('transactions.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        return view('transactions.quotations.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $quotationId = $data['quotation_id'] ?? ('QUO-' . strtoupper(Str::random(8)));

        $quotation = new Quotation();
        $quotation->fill([
            'quotation_id' => $quotationId,
            'quotation_date' => $data['quotation_date'] ?? now()->format('Y-m-d'),
            'customer_id' => $data['customer_id'],
            'contact_person_name' => $data['contact_person_name'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'address_line_1' => $data['address_line_1'] ?? null,
            'address_line_2' => $data['address_line_2'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? 'India',
            'validity' => $data['validity'] ?? null,
            'payment_terms' => $data['payment_terms'] ?? null,
            'inspection' => $data['inspection'] ?? null,
            'taxes' => $data['taxes'] ?? null,
            'freight' => $data['freight'] ?? null,
            'special_condition' => $data['special_condition'] ?? null,
        ]);

        $totalAmount = $this->calculateTotalFromItems($data['items'] ?? []);
        $quotation->total_amount = $totalAmount;

        $user = Auth::user();
        if ($user) {
            $quotation->organization_id = $user->organization_id ?? null;
            $quotation->branch_id = session('active_branch_id');
            $quotation->created_by = $user->id;
        }

        $quotation->save();

        foreach ($data['items'] as $index => $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],
                'item_description' => $item['item_description'] ?? null,
                'quantity' => $item['quantity'],
                'uom' => $item['uom'] ?? null,
                'price' => $item['price'],
                'amount' => (float) $item['quantity'] * (float) $item['price'],
                'sort_order' => $index,
            ]);
        }

        // Check if print is requested
        if ($request->has('print')) {
            return redirect(route('quotations.print', $quotation->id) . '?auto_print=1')
                ->with('success', 'Quotation created and saved successfully.');
        }

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.product']);
        return view('transactions.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();
        $quotation->load(['items', 'customer']);

        return view('transactions.quotations.edit', compact('quotation', 'customers', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $data = $this->validateRequest($request, $quotation);

        $quotation->quotation_date = $data['quotation_date'] ?? $quotation->quotation_date ?? now()->format('Y-m-d');
        $quotation->customer_id = $data['customer_id'];
        $quotation->contact_person_name = $data['contact_person_name'] ?? null;
        $quotation->contact_number = $data['contact_number'] ?? null;
        $quotation->postal_code = $data['postal_code'] ?? null;
        $quotation->company_name = $data['company_name'] ?? null;
        $quotation->address_line_1 = $data['address_line_1'] ?? null;
        $quotation->address_line_2 = $data['address_line_2'] ?? null;
        $quotation->city = $data['city'] ?? null;
        $quotation->state = $data['state'] ?? null;
        $quotation->country = $data['country'] ?? 'India';
        $quotation->validity = $data['validity'] ?? null;
        $quotation->payment_terms = $data['payment_terms'] ?? null;
        $quotation->inspection = $data['inspection'] ?? null;
        $quotation->taxes = $data['taxes'] ?? null;
        $quotation->freight = $data['freight'] ?? null;
        $quotation->special_condition = $data['special_condition'] ?? null;

        $totalAmount = $this->calculateTotalFromItems($data['items'] ?? []);
        $quotation->total_amount = $totalAmount;

        $quotation->save();

        $quotation->items()->delete();

        foreach ($data['items'] as $index => $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],
                'item_description' => $item['item_description'] ?? null,
                'quantity' => $item['quantity'],
                'uom' => $item['uom'] ?? null,
                'price' => $item['price'],
                'amount' => (float) $item['quantity'] * (float) $item['price'],
                'sort_order' => $index,
            ]);
        }

        // Check if print is requested
        if ($request->has('print')) {
            return redirect(route('quotations.print', $quotation->id) . '?auto_print=1')
                ->with('success', 'Quotation updated and saved successfully.');
        }

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    protected function validateRequest(Request $request, ?Quotation $quotation = null): array
    {
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'validity' => ['nullable', 'string'],
            'payment_terms' => ['required', 'string'],
            'inspection' => ['nullable', 'string'],
            'taxes' => ['required', 'string'],
            'freight' => ['nullable', 'string'],
            'special_condition' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ];

        if (!$quotation) {
            $rules['quotation_id'] = ['nullable', 'string', 'max:191', 'unique:quotations,quotation_id'];
        } else {
            $rules['quotation_id'] = ['nullable', 'string', 'max:191', 'unique:quotations,quotation_id,' . $quotation->id];
        }

        return $request->validate($rules);
    }

    protected function calculateTotalFromItems(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $total += (float) $item['quantity'] * (float) $item['price'];
        }
        return $total;
    }

    public function print(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.product']);
        
        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        return view('transactions.quotations.export-pdf', compact('quotation', 'companyInfo'));
    }
}

