<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier')->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($qs) use ($search) {
                        $qs->where('supplier_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $purchaseOrders = $query->paginate(15)->withQueryString();

        return view('transactions.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $rawMaterials = RawMaterial::orderBy('raw_material_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        return view('transactions.purchase-orders.create', compact('suppliers', 'rawMaterials', 'companyInfo'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        // Generate sequential Purchase Order Number starting from PUR001
        $allPurchaseOrders = PurchaseOrder::withTrashed()
            ->where('po_number', 'like', 'PUR%')
            ->get();

        $maxNumber = 0;
        foreach ($allPurchaseOrders as $po) {
            // Extract number from code (e.g., PUR001 -> 1, PUR123 -> 123)
            if (preg_match('/^PUR(\d+)$/i', $po->po_number, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Next number is max + 1, starting from 1 if no purchase orders exist
        $nextNumber = $maxNumber + 1;

        // Format as PUR001, PUR002, etc. (3 digits with leading zeros)
        $poNumber = 'PUR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Safety check: if code exists (shouldn't happen, but just in case), find next available
        $maxAttempts = 10000;
        $attempts = 0;
        while (PurchaseOrder::withTrashed()->where('po_number', $poNumber)->exists() && $attempts < $maxAttempts) {
            $nextNumber++;
            $poNumber = 'PUR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }

        $purchaseOrder = new PurchaseOrder();
        $purchaseOrder->fill([
            'po_number' => $poNumber,
            'supplier_id' => $data['supplier_id'],
            'purchase_date' => $data['purchase_date'],
            'delivery_date' => $data['delivery_date'] ?? null,
            'gst_percentage_overall' => $data['gst_percentage'] ?? 18,
            'gst_classification' => $data['gst_classification'] ?? $this->determineGstClassification($data['supplier_id']),
        ]);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? [], $data['gst_percentage'] ?? null);
        $purchaseOrder->total_raw_material_amount = $totals['total_raw_material_amount'];
        $purchaseOrder->total_gst_amount = $totals['total_gst_amount'];
        $purchaseOrder->grand_total = $totals['grand_total'];

        $user = Auth::user();
        if ($user) {
            $purchaseOrder->organization_id = $user->organization_id ?? null;
            $purchaseOrder->branch_id = session('active_branch_id');
            $purchaseOrder->created_by = $user->id;
        }

        $purchaseOrder->save();

        foreach ($data['items'] as $item) {
            $totalAmount = (float) $item['quantity'] * (float) $item['unit_price'];

            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'raw_material_id' => $item['raw_material_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $totalAmount,
                'gst_percentage' => null,
                'gst_amount' => 0,
                'line_total' => $totalAmount,
            ]);
        }

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.rawMaterial']);
        return view('transactions.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $rawMaterials = RawMaterial::orderBy('raw_material_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        $purchaseOrder->load('items');

        return view('transactions.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'rawMaterials', 'companyInfo'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $data = $this->validateRequest($request);

        $purchaseOrder->supplier_id = $data['supplier_id'];
        $purchaseOrder->purchase_date = $data['purchase_date'];
        $purchaseOrder->delivery_date = $data['delivery_date'] ?? null;
        $purchaseOrder->gst_percentage_overall = $data['gst_percentage'] ?? 18;
        $purchaseOrder->gst_classification = $data['gst_classification'] ?? $this->determineGstClassification($data['supplier_id']);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? [], $data['gst_percentage'] ?? null);
        $purchaseOrder->total_raw_material_amount = $totals['total_raw_material_amount'];
        $purchaseOrder->total_gst_amount = $totals['total_gst_amount'];
        $purchaseOrder->grand_total = $totals['grand_total'];

        $purchaseOrder->save();

        $purchaseOrder->items()->delete();

        foreach ($data['items'] as $item) {
            $totalAmount = (float) $item['quantity'] * (float) $item['unit_price'];

            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'raw_material_id' => $item['raw_material_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $totalAmount,
                'gst_percentage' => null,
                'gst_amount' => 0,
                'line_total' => $totalAmount,
            ]);
        }

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date'],

            'gst_percentage' => ['nullable', 'numeric', 'min:0'],
            'gst_classification' => ['nullable', 'in:CGST_SGST,IGST'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
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
        $totalRaw = 0;

        foreach ($items as $item) {
            $totalRaw += (float) $item['quantity'] * (float) $item['unit_price'];
        }

        $totalGst = $gstPercentage && $gstPercentage > 0 ? ($totalRaw * $gstPercentage) / 100 : 0;

        return [
            'total_raw_material_amount' => $totalRaw,
            'total_gst_amount' => $totalGst,
            'grand_total' => $totalRaw + $totalGst,
        ];
    }

    protected function determineGstClassification(int $supplierId): ?string
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return null;
        }

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        if (!$companyInfo || !$companyInfo->state || !$supplier->state) {
            return null;
        }

        return strtolower($companyInfo->state) === strtolower($supplier->state)
            ? 'CGST_SGST'
            : 'IGST';
    }
}


