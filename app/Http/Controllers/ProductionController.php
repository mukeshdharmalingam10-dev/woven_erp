<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\WorkOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $query = Production::with(['workOrder', 'product'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('workOrder', function ($qw) use ($search) {
                    $qw->where('work_order_number', 'like', "%{$search}%");
                })->orWhereHas('product', function ($qp) use ($search) {
                    $qp->where('product_name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $productions = $query->paginate(15)->withQueryString();

        return view('transactions.productions.index', compact('productions'));
    }

    public function create(Request $request)
    {
        // Only show open work orders (exclude completed ones)
        $workOrders = WorkOrder::where('status', WorkOrder::STATUS_OPEN)
            ->orderByDesc('id')
            ->get();
        $products = Product::orderBy('product_name')->get();

        $selectedWorkOrderId = $request->get('work_order_id');
        $selectedWorkOrder = null;
        if ($selectedWorkOrderId) {
            $selectedWorkOrder = WorkOrder::with('product')->find($selectedWorkOrderId);
        }

        return view('transactions.productions.create', compact('workOrders', 'products', 'selectedWorkOrder'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $totals = $this->calculateTotals($data['produced_quantity'], $data['weight_per_unit']);

        $production = new Production();
        $production->fill([
            'work_order_id' => $data['work_order_id'] ?? null,
            'product_id' => $data['product_id'],
            'produced_quantity' => $data['produced_quantity'],
            'weight_per_unit' => $data['weight_per_unit'],
            'total_weight' => $totals['total_weight'],
            'remarks' => $data['remarks'] ?? null,
        ]);

        $user = Auth::user();
        if ($user) {
            $production->organization_id = $user->organization_id ?? null;
            $production->branch_id = session('active_branch_id');
            $production->created_by = $user->id;
        }

        $production->save();
        
        // Auto-update work order status if work_order_id is set
        if ($production->work_order_id) {
            $this->updateWorkOrderStatus($production->work_order_id);
        }

        return redirect()->route('productions.index')
            ->with('success', 'Production entry created successfully.');
    }

    public function show(Production $production)
    {
        $production->load(['workOrder', 'product']);
        return view('transactions.productions.show', compact('production'));
    }

    public function edit(Production $production)
    {
        $production->load(['workOrder', 'product']);
        
        // Only show open work orders, but also include the work order already associated with this production (even if completed)
        $workOrders = WorkOrder::where(function($query) use ($production) {
            $query->where('status', WorkOrder::STATUS_OPEN)
                  ->orWhere('id', $production->work_order_id);
        })
        ->orderByDesc('id')
        ->get();
        
        $products = Product::orderBy('product_name')->get();
        
        return view('transactions.productions.edit', compact('production', 'workOrders', 'products'));
    }

    public function update(Request $request, Production $production)
    {
        $data = $this->validateRequest($request);

        $totals = $this->calculateTotals($data['produced_quantity'], $data['weight_per_unit']);

        $production->work_order_id = $data['work_order_id'] ?? null;
        $production->product_id = $data['product_id'];
        $production->produced_quantity = $data['produced_quantity'];
        $production->weight_per_unit = $data['weight_per_unit'];
        $production->total_weight = $totals['total_weight'];
        $production->remarks = $data['remarks'] ?? null;
        $oldWorkOrderId = $production->getOriginal('work_order_id');
        
        $production->save();
        
        // Auto-update work order status for both old and new work orders
        if ($oldWorkOrderId) {
            $this->updateWorkOrderStatus($oldWorkOrderId);
        }
        if ($production->work_order_id && $production->work_order_id != $oldWorkOrderId) {
            $this->updateWorkOrderStatus($production->work_order_id);
        }

        return redirect()->route('productions.index')
            ->with('success', 'Production entry updated successfully.');
    }

    public function destroy(Production $production)
    {
        $workOrderId = $production->work_order_id;

        $production->delete();
        
        // Auto-update work order status if work_order_id was set
        if ($workOrderId) {
            $this->updateWorkOrderStatus($workOrderId);
        }

        return redirect()->route('productions.index')
            ->with('success', 'Production entry deleted successfully.');
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'work_order_id' => ['nullable', 'exists:work_orders,id'],
            'product_id' => ['required', 'exists:products,id'],
            'produced_quantity' => ['required', 'integer', 'min:1'],
            'weight_per_unit' => ['required', 'numeric', 'min:0.0001'],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    protected function calculateTotals($producedQuantity, $weightPerUnit): array
    {
        $producedQuantity = (float) $producedQuantity;
        $weightPerUnit = (float) $weightPerUnit;

        $totalWeight = $producedQuantity * $weightPerUnit;
        // Round to whole number
        $totalWeight = round($totalWeight);

        return [
            'total_weight' => $totalWeight,
        ];
    }

    /**
     * Auto-update work order status based on production quantity
     */
    protected function updateWorkOrderStatus(int $workOrderId): void
    {
        $workOrder = WorkOrder::find($workOrderId);
        if (!$workOrder) {
            return;
        }

        // Calculate total produced quantity for this work order
        $totalProducedQuantity = Production::where('work_order_id', $workOrderId)
            ->sum('produced_quantity');

        // Update status: completed if total production >= quantity to produce
        if ($totalProducedQuantity >= (float) $workOrder->quantity_to_produce && $workOrder->quantity_to_produce > 0) {
            $workOrder->status = WorkOrder::STATUS_COMPLETED;
        } else {
            $workOrder->status = WorkOrder::STATUS_OPEN;
        }

        $workOrder->save();
    }
}


