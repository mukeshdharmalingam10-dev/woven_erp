<?php

namespace App\Http\Controllers;

use App\Models\MaterialInward;
use App\Models\MaterialInwardItem;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MaterialInwardController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialInward::with('supplier')->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('inward_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($qs) use ($search) {
                        $qs->where('supplier_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $inwards = $query->paginate(15)->withQueryString();

        return view('transactions.material-inwards.index', compact('inwards'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $rawMaterials = RawMaterial::orderBy('raw_material_name')->get();
        $purchaseOrders = PurchaseOrder::orderBy('po_number')->get();

        return view('transactions.material-inwards.create', compact('suppliers', 'rawMaterials', 'purchaseOrders'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $inwardNumber = 'MI-' . strtoupper(Str::random(8));

        $totalAmount = $this->calculateTotalAmount($data['items']);

        $inward = new MaterialInward();
        $inward->fill([
            'inward_number' => $inwardNumber,
            'received_date' => $data['received_date'],
            'supplier_id' => $data['supplier_id'],
            'purchase_order_id' => $data['purchase_order_id'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'total_amount' => $totalAmount,
        ]);

        $user = Auth::user();
        if ($user) {
            $inward->organization_id = $user->organization_id ?? null;
            $inward->branch_id = session('active_branch_id');
            $inward->created_by = $user->id;
        }

        $inward->save();

        foreach ($data['items'] as $item) {
            $qty = (float) $item['quantity_received'];
            $price = (float) $item['unit_price'];
            $lineTotal = $qty * $price;

            MaterialInwardItem::create([
                'material_inward_id' => $inward->id,
                'raw_material_id' => $item['raw_material_id'],
                'quantity_received' => $qty,
                'unit_of_measure' => $item['unit_of_measure'],
                'unit_price' => $price,
                'total_amount' => $lineTotal,
            ]);
        }

        return redirect()->route('material-inwards.index')
            ->with('success', 'Material Inward created successfully.');
    }

    public function show(MaterialInward $materialInward)
    {
        $materialInward->load(['supplier', 'purchaseOrder', 'items.rawMaterial']);
        return view('transactions.material-inwards.show', compact('materialInward'));
    }

    public function edit(MaterialInward $materialInward)
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $rawMaterials = RawMaterial::orderBy('raw_material_name')->get();
        $purchaseOrders = PurchaseOrder::orderBy('po_number')->get();

        $materialInward->load('items');

        return view('transactions.material-inwards.edit', compact('materialInward', 'suppliers', 'rawMaterials', 'purchaseOrders'));
    }

    public function update(Request $request, MaterialInward $materialInward)
    {
        $data = $this->validateRequest($request);

        $totalAmount = $this->calculateTotalAmount($data['items']);

        $materialInward->received_date = $data['received_date'];
        $materialInward->supplier_id = $data['supplier_id'];
        $materialInward->purchase_order_id = $data['purchase_order_id'] ?? null;
        $materialInward->remarks = $data['remarks'] ?? null;
        $materialInward->total_amount = $totalAmount;
        $materialInward->save();

        $materialInward->items()->delete();

        foreach ($data['items'] as $item) {
            $qty = (float) $item['quantity_received'];
            $price = (float) $item['unit_price'];
            $lineTotal = $qty * $price;

            MaterialInwardItem::create([
                'material_inward_id' => $materialInward->id,
                'raw_material_id' => $item['raw_material_id'],
                'quantity_received' => $qty,
                'unit_of_measure' => $item['unit_of_measure'],
                'unit_price' => $price,
                'total_amount' => $lineTotal,
            ]);
        }

        return redirect()->route('material-inwards.index')
            ->with('success', 'Material Inward updated successfully.');
    }

    public function destroy(MaterialInward $materialInward)
    {
        $materialInward->delete();

        return redirect()->route('material-inwards.index')
            ->with('success', 'Material Inward deleted successfully.');
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'received_date' => ['required', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'remarks' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'items.*.quantity_received' => ['required', 'integer', 'min:1'],
            'items.*.unit_of_measure' => ['required', 'string', 'max:191'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    protected function calculateTotalAmount(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $qty = (float) $item['quantity_received'];
            $price = (float) $item['unit_price'];
            $total += $qty * $price;
        }
        return $total;
    }
}


