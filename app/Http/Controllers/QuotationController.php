<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('supplier')->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_id', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($qs) use ($search) {
                        $qs->where('supplier_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $quotations = $query->paginate(15)->withQueryString();

        return view('transactions.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $products = Product::orderBy('product_name')->get();

        return view('transactions.quotations.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $quotationId = $data['quotation_id'] ?? ('QUO-' . strtoupper(Str::random(8)));

        $quotation = new Quotation();
        $quotation->fill([
            'quotation_id' => $quotationId,
            'quotation_date' => $data['quotation_date'] ?? now()->format('Y-m-d'),
            'supplier_id' => $data['supplier_id'],
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
        $quotation->load(['supplier', 'items.product']);
        return view('transactions.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $products = Product::orderBy('product_name')->get();
        $quotation->load(['items', 'supplier']);

        return view('transactions.quotations.edit', compact('quotation', 'suppliers', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $data = $this->validateRequest($request, $quotation);

        $quotation->quotation_date = $data['quotation_date'] ?? $quotation->quotation_date ?? now()->format('Y-m-d');
        $quotation->supplier_id = $data['supplier_id'];
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
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'validity' => ['required', 'string'],
            'payment_terms' => ['required', 'string'],
            'inspection' => ['required', 'string'],
            'taxes' => ['required', 'string'],
            'freight' => ['required', 'string'],
            'special_condition' => ['required', 'string'],
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
        $quotation->load(['supplier', 'items.product']);
        
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

