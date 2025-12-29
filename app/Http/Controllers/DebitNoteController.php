<?php

namespace App\Http\Controllers;

use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\PurchaseOrder;
use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = DebitNote::with(['supplier', 'customer'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('debit_note_number', 'like', "%{$search}%")
                    ->orWhere('party_name', 'like', "%{$search}%")
                    ->orWhere('reference_document_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $debitNotes = $query->paginate(15)->withQueryString();

        return view('transactions.debit-notes.index', compact('debitNotes'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        // Get reference documents
        $salesInvoices = SalesInvoice::orderByDesc('invoice_date')->get();
        // Note: Purchase Invoice and Dispatch models would be added here when available

        // Map suppliers and customers for JavaScript
        $suppliersMapped = $suppliers->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->supplier_name,
                'code' => $s->code ?? '',
                'gst' => $s->gst_number ?? '',
                'state' => $s->state ?? ''
            ];
        });
        
        $customersMapped = $customers->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->customer_name,
                'code' => $c->code ?? '',
                'gst' => $c->gst_number ?? '',
                'state' => $c->billing_state ?? ''
            ];
        });

        return view('transactions.debit-notes.create', compact('suppliers', 'customers', 'products', 'companyInfo', 'salesInvoices', 'suppliersMapped', 'customersMapped'));
    }

    public function store(Request $request)
    {
        // Clean up reference item values before validation
        $request = $this->cleanReferenceItemValues($request);
        $data = $this->validateRequest($request);

        $debitNoteNumber = $data['debit_note_number'] ?? ('DN-' . strtoupper(Str::random(8)));

        // Determine party type and ID
        $partyType = $data['party_type'] ?? null;
        $partyId = $data['party_id'] ?? null;
        $partyName = $data['party_name'] ?? null;
        $gstNumber = null;

        // If reference document is selected, auto-populate party details
        if (!empty($data['reference_document_type']) && $data['reference_document_type'] !== 'Manual') {
            $partyInfo = $this->getPartyInfoFromReference($data['reference_document_type'], $data['reference_document_id'] ?? null);
            if ($partyInfo) {
                $partyType = $partyInfo['party_type'];
                $partyId = $partyInfo['party_id'];
                $partyName = $partyInfo['party_name'];
                $gstNumber = $partyInfo['gst_number'] ?? null;
            }
        } else {
            // For manual entry, get party details from selected party
            if ($partyType === 'Supplier' && $partyId) {
                $supplier = Supplier::find($partyId);
                if ($supplier) {
                    $partyName = $supplier->supplier_name;
                    $gstNumber = $supplier->gst_number ?? null;
                }
            } elseif ($partyType === 'Customer' && $partyId) {
                $customer = Customer::find($partyId);
                if ($customer) {
                    $partyName = $customer->customer_name;
                    $gstNumber = $customer->gst_number ?? null;
                }
            }
        }

        // Determine GST classification if not provided
        $gstClassification = $data['gst_classification'] ?? $this->determineGstClassification($partyType, $partyId);

        $debitNote = new DebitNote();
        $debitNote->fill([
            'debit_note_number' => $debitNoteNumber,
            'debit_note_date' => $data['debit_note_date'],
            'reference_document_type' => $data['reference_document_type'] ?? 'Manual',
            'reference_document_number' => $data['reference_document_number'] ?? null,
            'reference_document_id' => $data['reference_document_id'] ?? null,
            'party_type' => $partyType,
            'party_id' => $partyId,
            'party_name' => $partyName,
            'gst_number' => $gstNumber,
            'currency' => $data['currency'] ?? 'INR',
            'gst_classification' => $gstClassification,
            'gst_percentage' => $data['gst_percentage'] ?? 18,
            'debit_note_reason' => $data['debit_note_reason'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'status' => 'Draft',
        ]);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? []);
        $debitNote->subtotal = $totals['subtotal'];
        $debitNote->cgst_amount = $totals['cgst_amount'];
        $debitNote->sgst_amount = $totals['sgst_amount'];
        $debitNote->igst_amount = $totals['igst_amount'];
        $debitNote->adjustments = $data['adjustments'] ?? 0;
        $debitNote->total_debit_amount = $totals['total'] + ($data['adjustments'] ?? 0);

        $user = Auth::user();
        if ($user) {
            $debitNote->organization_id = $user->organization_id ?? null;
            $debitNote->branch_id = session('active_branch_id');
            $debitNote->created_by = $user->id;
        }

        $debitNote->save();

        foreach ($data['items'] as $item) {
            // Ensure product_id is null if empty or invalid
            $productId = !empty($item['product_id']) && is_numeric($item['product_id']) ? (int) $item['product_id'] : null;
            
            DebitNoteItem::create([
                'debit_note_id' => $debitNote->id,
                'product_id' => $productId,
                'item_name' => $item['item_name'] ?? null,
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit_of_measure' => $item['unit_of_measure'] ?? null,
                'rate' => $item['rate'] ?? 0,
                'amount' => ($item['quantity'] ?? 0) * ($item['rate'] ?? 0),
                'cgst_percentage' => $item['cgst_percentage'] ?? 0,
                'cgst_amount' => $item['cgst_amount'] ?? 0,
                'sgst_percentage' => $item['sgst_percentage'] ?? 0,
                'sgst_amount' => $item['sgst_amount'] ?? 0,
                'igst_percentage' => $item['igst_percentage'] ?? 0,
                'igst_amount' => $item['igst_amount'] ?? 0,
                'line_total' => $item['line_total'] ?? 0,
            ]);
        }

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit Note created successfully.');
    }

    public function show(DebitNote $debitNote)
    {
        $debitNote->load(['items.product', 'supplier', 'customer', 'creator', 'submitter']);
        return view('transactions.debit-notes.show', compact('debitNote'));
    }

    public function edit(DebitNote $debitNote)
    {
        if ($debitNote->isSubmitted()) {
            return redirect()->route('debit-notes.show', $debitNote)
                ->with('error', 'Submitted debit notes cannot be edited.');
        }

        $suppliers = Supplier::orderBy('supplier_name')->get();
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        $salesInvoices = SalesInvoice::orderByDesc('invoice_date')->get();

        $debitNote->load(['items', 'supplier', 'customer']);

        // Map suppliers and customers for JavaScript
        $suppliersMapped = $suppliers->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->supplier_name,
                'code' => $s->code ?? '',
                'gst' => $s->gst_number ?? '',
                'state' => $s->state ?? ''
            ];
        });
        
        $customersMapped = $customers->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->customer_name,
                'code' => $c->code ?? '',
                'gst' => $c->gst_number ?? '',
                'state' => $c->billing_state ?? ''
            ];
        });

        return view('transactions.debit-notes.edit', compact('debitNote', 'suppliers', 'customers', 'products', 'companyInfo', 'salesInvoices', 'suppliersMapped', 'customersMapped'));
    }

    public function update(Request $request, DebitNote $debitNote)
    {
        if ($debitNote->isSubmitted()) {
            return redirect()->route('debit-notes.show', $debitNote)
                ->with('error', 'Submitted debit notes cannot be edited.');
        }

        // Clean up reference item values before validation
        $request = $this->cleanReferenceItemValues($request);
        $data = $this->validateRequest($request, $debitNote);

        // Determine party type and ID
        $partyType = $data['party_type'] ?? null;
        $partyId = $data['party_id'] ?? null;
        $partyName = $data['party_name'] ?? null;
        $gstNumber = null;

        // If reference document is selected, auto-populate party details
        if (!empty($data['reference_document_type']) && $data['reference_document_type'] !== 'Manual') {
            $partyInfo = $this->getPartyInfoFromReference($data['reference_document_type'], $data['reference_document_id'] ?? null);
            if ($partyInfo) {
                $partyType = $partyInfo['party_type'];
                $partyId = $partyInfo['party_id'];
                $partyName = $partyInfo['party_name'];
                $gstNumber = $partyInfo['gst_number'] ?? null;
            }
        } else {
            // For manual entry, get party details from selected party
            if ($partyType === 'Supplier' && $partyId) {
                $supplier = Supplier::find($partyId);
                if ($supplier) {
                    $partyName = $supplier->supplier_name;
                    $gstNumber = $supplier->gst_number ?? null;
                }
            } elseif ($partyType === 'Customer' && $partyId) {
                $customer = Customer::find($partyId);
                if ($customer) {
                    $partyName = $customer->customer_name;
                    $gstNumber = $customer->gst_number ?? null;
                }
            }
        }

        // Determine GST classification if not provided
        $gstClassification = $data['gst_classification'] ?? $this->determineGstClassification($partyType, $partyId);

        $debitNote->debit_note_number = $data['debit_note_number'] ?? $debitNote->debit_note_number;
        $debitNote->debit_note_date = $data['debit_note_date'];
        $debitNote->reference_document_type = $data['reference_document_type'] ?? 'Manual';
        $debitNote->reference_document_number = $data['reference_document_number'] ?? null;
        $debitNote->reference_document_id = $data['reference_document_id'] ?? null;
        $debitNote->party_type = $partyType;
        $debitNote->party_id = $partyId;
        $debitNote->party_name = $partyName;
        $debitNote->gst_number = $gstNumber;
        $debitNote->currency = $data['currency'] ?? 'INR';
        $debitNote->gst_classification = $gstClassification;
        $debitNote->gst_percentage = $data['gst_percentage'] ?? 18;
        $debitNote->debit_note_reason = $data['debit_note_reason'] ?? null;
        $debitNote->remarks = $data['remarks'] ?? null;

        $totals = $this->calculateTotalsFromItems($data['items'] ?? []);
        $debitNote->subtotal = $totals['subtotal'];
        $debitNote->cgst_amount = $totals['cgst_amount'];
        $debitNote->sgst_amount = $totals['sgst_amount'];
        $debitNote->igst_amount = $totals['igst_amount'];
        $debitNote->adjustments = $data['adjustments'] ?? 0;
        $debitNote->total_debit_amount = $totals['total'] + ($data['adjustments'] ?? 0);

        $user = Auth::user();
        if ($user) {
            $debitNote->updated_by = $user->id;
        }

        $debitNote->save();

        $debitNote->items()->delete();

        foreach ($data['items'] as $item) {
            // Ensure product_id is null if empty or invalid
            $productId = !empty($item['product_id']) && is_numeric($item['product_id']) ? (int) $item['product_id'] : null;
            
            DebitNoteItem::create([
                'debit_note_id' => $debitNote->id,
                'product_id' => $productId,
                'item_name' => $item['item_name'] ?? null,
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit_of_measure' => $item['unit_of_measure'] ?? null,
                'rate' => $item['rate'] ?? 0,
                'amount' => ($item['quantity'] ?? 0) * ($item['rate'] ?? 0),
                'cgst_percentage' => $item['cgst_percentage'] ?? 0,
                'cgst_amount' => $item['cgst_amount'] ?? 0,
                'sgst_percentage' => $item['sgst_percentage'] ?? 0,
                'sgst_amount' => $item['sgst_amount'] ?? 0,
                'igst_percentage' => $item['igst_percentage'] ?? 0,
                'igst_amount' => $item['igst_amount'] ?? 0,
                'line_total' => $item['line_total'] ?? 0,
            ]);
        }

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit Note updated successfully.');
    }

    public function destroy(DebitNote $debitNote)
    {
        if ($debitNote->isSubmitted()) {
            return redirect()->route('debit-notes.index')
                ->with('error', 'Submitted debit notes cannot be deleted.');
        }

        $debitNote->delete();

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit Note deleted successfully.');
    }

    public function submit(Request $request, DebitNote $debitNote)
    {
        if (!$debitNote->isDraft()) {
            return redirect()->route('debit-notes.show', $debitNote)
                ->with('error', 'Only draft debit notes can be submitted.');
        }

        $debitNote->status = 'Submitted';
        $debitNote->submitted_by = Auth::id();
        $debitNote->submitted_at = now();
        $debitNote->save();

        // Handle stock impact for Purchase Return and Short Supply
        if (in_array($debitNote->debit_note_reason, ['Purchase Return', 'Short Supply'])) {
            $this->handleStockImpact($debitNote);
        }

        return redirect()->route('debit-notes.show', $debitNote)
            ->with('success', 'Debit Note submitted successfully.');
    }

    public function cancel(Request $request, DebitNote $debitNote)
    {
        $request->validate([
            'cancel_reason' => 'required|string|min:10',
        ]);

        if ($debitNote->isCancelled()) {
            return redirect()->route('debit-notes.show', $debitNote)
                ->with('error', 'Debit Note is already cancelled.');
        }

        $debitNote->status = 'Cancelled';
        $debitNote->cancel_reason = $request->cancel_reason;
        $debitNote->save();

        return redirect()->route('debit-notes.show', $debitNote)
            ->with('success', 'Debit Note cancelled successfully.');
    }

    public function getReferenceDocuments(Request $request)
    {
        $type = $request->get('type');
        $documents = [];
        
        $activeBranchId = session('active_branch_id');
        $user = Auth::user();
        $organizationId = $user ? $user->organization_id : null;

        switch ($type) {
            case 'Sales Invoice':
                $query = SalesInvoice::select('id', 'invoice_number', 'invoice_date');
                
                // Filter by branch if active branch is set
                if ($activeBranchId) {
                    $query->where('branch_id', $activeBranchId);
                }
                
                // Filter by organization if set
                if ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }
                
                $documents = $query->orderByDesc('invoice_date')
                    ->get()
                    ->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'number' => $doc->invoice_number,
                            'date' => $doc->invoice_date ? $doc->invoice_date->format('Y-m-d') : '',
                        ];
                    })
                    ->values()
                    ->toArray();
                break;
            case 'Purchase Invoice':
                $query = PurchaseOrder::select('id', 'po_number');
                
                // Filter by branch if active branch is set
                if ($activeBranchId) {
                    $query->where('branch_id', $activeBranchId);
                }
                
                // Filter by organization if set
                if ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }
                
                $documents = $query->orderBy('po_number')
                    ->get()
                    ->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'number' => $doc->po_number,
                        ];
                    })
                    ->values()
                    ->toArray();
                break;
            // Add cases for Dispatch when model is available
        }

        return response()->json($documents);
    }

    public function getReferenceDocumentDetails(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        $details = null;

        switch ($type) {
            case 'Sales Invoice':
                $invoice = SalesInvoice::with(['customer', 'items.product'])->find($id);
                if ($invoice) {
                    $details = [
                        'party_type' => 'Customer',
                        'party_id' => $invoice->customer_id,
                        'party_name' => $invoice->customer->customer_name ?? null,
                        'gst_number' => $invoice->customer->gst_number ?? null,
                        'currency' => 'INR',
                        'items' => $invoice->items->map(function ($item) {
                            return [
                                'product_id' => $item->product_id,
                                'item_name' => $item->product->product_name ?? null,
                                'description' => $item->description,
                                'quantity' => $item->quantity_sold,
                                'unit_of_measure' => $item->product->unit_of_measure ?? null,
                                'rate' => $item->unit_price,
                            ];
                        })->values()->toArray(),
                    ];
                }
                break;
            case 'Purchase Invoice':
                $purchaseOrder = PurchaseOrder::with(['supplier', 'items.rawMaterial'])->find($id);
                if ($purchaseOrder) {
                    $details = [
                        'party_type' => 'Supplier',
                        'party_id' => $purchaseOrder->supplier_id,
                        'party_name' => $purchaseOrder->supplier->supplier_name ?? null,
                        'gst_number' => $purchaseOrder->supplier->gst_number ?? null,
                        'currency' => 'INR',
                        'items' => $purchaseOrder->items->map(function ($item) {
                            // Check if rawMaterial exists before accessing its properties
                            if (!$item->rawMaterial) {
                                return null;
                            }
                            return [
                                'product_id' => null, // Raw materials are not products, so set to null
                                'item_name' => $item->rawMaterial->raw_material_name ?? null,
                                'description' => '',
                                'quantity' => $item->quantity ?? 0,
                                'unit_of_measure' => $item->rawMaterial->unit_of_measure ?? null,
                                'rate' => $item->unit_price ?? 0,
                            ];
                        })->filter(function ($item) {
                            // Filter out null items and items without item_name
                            return $item !== null && !empty($item['item_name']);
                        })->values()->toArray(),
                    ];
                }
                break;
            // Add cases for Dispatch when model is available
        }

        return response()->json($details);
    }

    protected function validateRequest(Request $request, ?DebitNote $debitNote = null): array
    {
        $rules = [
            'debit_note_date' => ['required', 'date'],
            'reference_document_type' => ['nullable', 'in:Purchase Invoice,Sales Invoice,Dispatch,Manual'],
            'reference_document_id' => ['nullable', 'integer'],
            'party_type' => ['nullable', 'in:Supplier,Customer'],
            'party_id' => ['nullable', 'integer'],
            'party_name' => ['nullable', 'string', 'max:191'],
            'currency' => ['nullable', 'string', 'max:10'],
            'gst_classification' => ['nullable', 'in:CGST_SGST,IGST'],
            'gst_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'debit_note_reason' => ['nullable', 'in:Purchase Return,Rate Difference,Short Supply,Damage Compensation,Others'],
            'remarks' => ['nullable', 'string'],
            'adjustments' => ['nullable', 'numeric'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'sometimes', 'exists:products,id'],
            'items.*.item_name' => ['nullable', 'string', 'max:191'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.rate' => ['nullable', 'numeric', 'min:0'],
        ];

        // Remarks is mandatory when reason is Others
        if ($request->debit_note_reason === 'Others') {
            $rules['remarks'] = ['required', 'string', 'min:10'];
        }

        if (!$debitNote) {
            $rules['debit_note_number'] = ['nullable', 'string', 'max:191', 'unique:debit_notes,debit_note_number'];
        } else {
            $rules['debit_note_number'] = ['nullable', 'string', 'max:191', 'unique:debit_notes,debit_note_number,' . $debitNote->id];
        }

        return $request->validate($rules);
    }

    protected function calculateTotalsFromItems(array $items): array
    {
        $subtotal = 0;
        $cgstAmount = 0;
        $sgstAmount = 0;
        $igstAmount = 0;

        foreach ($items as $item) {
            $amount = (float) ($item['quantity'] ?? 0) * (float) ($item['rate'] ?? 0);
            $subtotal += $amount;
            $cgstAmount += (float) ($item['cgst_amount'] ?? 0);
            $sgstAmount += (float) ($item['sgst_amount'] ?? 0);
            $igstAmount += (float) ($item['igst_amount'] ?? 0);
        }

        return [
            'subtotal' => $subtotal,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'total' => $subtotal + $cgstAmount + $sgstAmount + $igstAmount,
        ];
    }

    protected function getPartyInfoFromReference(string $type, ?int $documentId): ?array
    {
        switch ($type) {
            case 'Sales Invoice':
                $invoice = SalesInvoice::with('customer')->find($documentId);
                if ($invoice && $invoice->customer) {
                    return [
                        'party_type' => 'Customer',
                        'party_id' => $invoice->customer_id,
                        'party_name' => $invoice->customer->customer_name,
                        'gst_number' => $invoice->customer->gst_number ?? null,
                    ];
                }
                break;
            case 'Purchase Invoice':
                $purchaseOrder = PurchaseOrder::with('supplier')->find($documentId);
                if ($purchaseOrder && $purchaseOrder->supplier) {
                    return [
                        'party_type' => 'Supplier',
                        'party_id' => $purchaseOrder->supplier_id,
                        'party_name' => $purchaseOrder->supplier->supplier_name,
                        'gst_number' => $purchaseOrder->supplier->gst_number ?? null,
                    ];
                }
                break;
            // Add cases for Dispatch when model is available
        }

        return null;
    }

    protected function determineGstClassification(?string $partyType, ?int $partyId): ?string
    {
        if (!$partyType || !$partyId) {
            return 'CGST_SGST'; // Default
        }

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        if (!$companyInfo || !$companyInfo->state) {
            return 'CGST_SGST'; // Default
        }

        $partyState = null;

        if ($partyType === 'Supplier') {
            $supplier = Supplier::find($partyId);
            if ($supplier && $supplier->state) {
                $partyState = $supplier->state;
            }
        } elseif ($partyType === 'Customer') {
            $customer = Customer::find($partyId);
            if ($customer && $customer->billing_state) {
                $partyState = $customer->billing_state;
            }
        }

        if (!$partyState) {
            return 'CGST_SGST'; // Default
        }

        return strtolower($companyInfo->state) === strtolower($partyState)
            ? 'CGST_SGST'
            : 'IGST';
    }

    protected function handleStockImpact(DebitNote $debitNote)
    {
        // This would integrate with StockTransaction model
        // For now, it's a placeholder
        // When Purchase Return or Short Supply is submitted, reduce stock
        foreach ($debitNote->items as $item) {
            if ($item->product_id && $item->quantity > 0) {
                // Create stock transaction to reduce stock
                // StockTransaction::create([...]);
            }
        }
    }

    protected function cleanReferenceItemValues(Request $request): Request
    {
        // Clean up reference item values (ref_0, ref_1, etc.) to null
        $items = $request->input('items', []);
        foreach ($items as $index => $item) {
            if (isset($item['product_id'])) {
                // Convert reference item values or empty strings to null
                if (is_string($item['product_id']) && strpos($item['product_id'], 'ref_') === 0) {
                    $items[$index]['product_id'] = null;
                } elseif ($item['product_id'] === '' || $item['product_id'] === '0') {
                    // Convert empty strings or '0' to null for integer columns
                    $items[$index]['product_id'] = null;
                } elseif (!empty($item['product_id'])) {
                    // Ensure valid integer
                    $items[$index]['product_id'] = (int) $item['product_id'];
                } else {
                    $items[$index]['product_id'] = null;
                }
            } else {
                $items[$index]['product_id'] = null;
            }
        }
        $request->merge(['items' => $items]);
        return $request;
    }
}
