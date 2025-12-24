<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::with(['customer', 'product'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('work_order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($qp) use ($search) {
                        $qp->where('product_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $workOrders = $query->paginate(15)->withQueryString();

        return view('transactions.work-orders.index', compact('workOrders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        return view('transactions.work-orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        // Generate sequential Work Order Number starting from WON001
        $allWorkOrders = WorkOrder::withTrashed()
            ->where('work_order_number', 'like', 'WON%')
            ->get();

        $maxNumber = 0;
        foreach ($allWorkOrders as $wo) {
            // Extract number from code (e.g., WON001 -> 1, WON123 -> 123)
            if (preg_match('/^WON(\d+)$/i', $wo->work_order_number, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Next number is max + 1, starting from 1 if no work orders exist
        $nextNumber = $maxNumber + 1;

        // Format as WON001, WON002, etc. (3 digits with leading zeros)
        $workOrderNumber = 'WON' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Safety check: if code exists (shouldn't happen, but just in case), find next available
        $maxAttempts = 10000;
        $attempts = 0;
        while (WorkOrder::withTrashed()->where('work_order_number', $workOrderNumber)->exists() && $attempts < $maxAttempts) {
            $nextNumber++;
            $workOrderNumber = 'WON' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }

        $workOrder = new WorkOrder();
        $workOrder->fill([
            'work_order_number' => $workOrderNumber,
            'customer_id' => $data['customer_id'],
            'product_id' => $data['product_id'],
            'quantity_to_produce' => $data['quantity_to_produce'],
            'per_kg_weight' => $data['per_kg_weight'] ?? null,
            'work_order_date' => $data['work_order_date'],
            'status' => WorkOrder::STATUS_OPEN,
        ]);

        $user = Auth::user();
        if ($user) {
            $workOrder->organization_id = $user->organization_id ?? null;
            $workOrder->branch_id = session('active_branch_id');
            $workOrder->created_by = $user->id;
        }

        $workOrder->save();

        return redirect()->route('work-orders.index')
            ->with('success', 'Work Order created successfully.');
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['customer', 'product', 'materials.rawMaterial']);
        return view('transactions.work-orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        return view('transactions.work-orders.edit', compact('workOrder', 'customers', 'products'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $data = $this->validateRequest($request);

        $workOrder->customer_id = $data['customer_id'];
        $workOrder->product_id = $data['product_id'];
        $workOrder->quantity_to_produce = $data['quantity_to_produce'];
        $workOrder->per_kg_weight = $data['per_kg_weight'] ?? null;
        $workOrder->work_order_date = $data['work_order_date'];

        // Auto update status: completed if total production quantity >= quantity_to_produce
        $totalProducedQuantity = $workOrder->productions()->sum('produced_quantity');
        if ($totalProducedQuantity >= (float) $workOrder->quantity_to_produce && $workOrder->quantity_to_produce > 0) {
            $workOrder->status = WorkOrder::STATUS_COMPLETED;
        } else {
            $workOrder->status = WorkOrder::STATUS_OPEN;
        }

        $workOrder->save();

        return redirect()->route('work-orders.index')
            ->with('success', 'Work Order updated successfully.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'Work Order deleted successfully.');
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity_to_produce' => ['required', 'integer', 'min:1'],
            'per_kg_weight' => ['nullable', 'numeric', 'min:0'],
            'work_order_date' => ['required', 'date'],
        ]);
    }
}


