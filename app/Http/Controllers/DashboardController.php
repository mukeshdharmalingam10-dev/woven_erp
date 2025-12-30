<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Employee;
use App\Models\WorkOrder;
use App\Models\Production;
use App\Models\SalesInvoice;
use App\Models\PurchaseOrder;
use App\Models\PettyCash;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user) {
            $user->load(['role', 'entity', 'organization', 'branch']);
        }
        
        // Get statistics with branch filtering
        $stats = [
            'customers' => $this->getCount(Customer::class),
            'products' => $this->getCount(Product::class),
            'raw_materials' => $this->getCount(RawMaterial::class),
            'employees' => $this->getCount(Employee::class),
            'work_orders' => $this->getCount(WorkOrder::class),
            'productions' => $this->getCount(Production::class),
            'sales_invoices' => $this->getCount(SalesInvoice::class),
            'purchase_orders' => $this->getCount(PurchaseOrder::class),
        ];

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get monthly sales data for chart (last 6 months)
        $monthlySales = $this->getMonthlySales();

        return view('dashboard.index', compact('user', 'stats', 'recentActivities', 'monthlySales'));
    }

    /**
     * Get count for a model with branch filtering
     * Shows all records including those with NULL branch_id
     */
    private function getCount($modelClass)
    {
        try {
            $query = $modelClass::query();
            
            // Apply branch filter if applicable
            $branchId = session('active_branch_id');
            
            // Check if model has branch_id column
            $modelInstance = new $modelClass;
            $tableName = $modelInstance->getTable();
            
            if ($branchId && Schema::hasColumn($tableName, 'branch_id')) {
                // Include records that match the branch OR have NULL branch_id (shared/master data)
                $query->where(function($q) use ($tableName, $branchId) {
                    $q->where($tableName . '.branch_id', $branchId)
                      ->orWhereNull($tableName . '.branch_id');
                });
            }
            
            // Note: Soft-deleted records are automatically excluded by Eloquent
            // when using SoftDeletes trait
            
            return $query->count();
        } catch (\Exception $e) {
            // If there's an error, try to get count without branch filter
            try {
                return $modelClass::count();
            } catch (\Exception $e2) {
                return 0;
            }
        }
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        $activities = [];
        $user = auth()->user();
        $branchId = session('active_branch_id');

        // Recent Work Orders
        $workOrders = WorkOrder::when($branchId, function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->latest()->limit(5)->get();
        
        foreach ($workOrders as $wo) {
            $activities[] = [
                'type' => 'work_order',
                'icon' => 'fa-file-alt',
                'color' => '#667eea',
                'title' => 'New Work Order',
                'description' => "WO: {$wo->work_order_number}",
                'time' => $wo->created_at->diffForHumans(),
                'date' => $wo->created_at,
            ];
        }

        // Recent Productions
        $productions = Production::when($branchId, function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->latest()->limit(5)->get();
        
        foreach ($productions as $prod) {
            $activities[] = [
                'type' => 'production',
                'icon' => 'fa-industry',
                'color' => '#28a745',
                'title' => 'Production Entry',
                'description' => "Produced: {$prod->produced_quantity} units",
                'time' => $prod->created_at->diffForHumans(),
                'date' => $prod->created_at,
            ];
        }

        // Recent Sales Invoices
        $salesInvoices = SalesInvoice::when($branchId, function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->latest()->limit(5)->get();
        
        foreach ($salesInvoices as $invoice) {
            $activities[] = [
                'type' => 'sales',
                'icon' => 'fa-file-invoice-dollar',
                'color' => '#17a2b8',
                'title' => 'Sales Invoice',
                'description' => "Invoice: {$invoice->invoice_number}",
                'time' => $invoice->created_at->diffForHumans(),
                'date' => $invoice->created_at,
            ];
        }

        // Sort by date and get latest 10
        usort($activities, function($a, $b) {   
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get monthly sales data
     */
    private function getMonthlySales()
    {
        $branchId = session('active_branch_id');
        
        $sales = SalesInvoice::when($branchId, function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(grand_total) as total')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        return $sales;
    }
}
