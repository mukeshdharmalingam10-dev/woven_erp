<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\RawMaterial;
use App\Models\Product;
use App\Models\MaterialInward;
use App\Models\MaterialInwardItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Production;
use App\Models\WorkOrderMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('source_document_number', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('transaction_type')) {
            $query->where('transaction_type', $type);
        }

        $stockTransactions = $query->paginate(15)->withQueryString();

        return view('transactions.stock-transactions.index', compact('stockTransactions'));
    }

    public function create()
    {
        $rawMaterials = RawMaterial::orderBy('raw_material_name')->get();
        $products = Product::orderBy('product_name')->get();
        $materialInwards = MaterialInward::orderByDesc('id')->get();
        $salesInvoices = SalesInvoice::orderByDesc('id')->get();

        return view('transactions.stock-transactions.create', compact('rawMaterials', 'products', 'materialInwards', 'salesInvoices'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $transactionNumber = $data['transaction_number'] ?? ('ST-' . strtoupper(Str::random(8)));

        // Get item details
        $item = null;
        $unitOfMeasure = '';
        if ($data['item_type'] === 'raw_material') {
            $item = RawMaterial::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        } else {
            $item = Product::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        }

        // Validate stock availability for stock out
        if ($data['transaction_type'] === 'stock_out') {
            $availableStock = $this->getAvailableStock($data['item_type'], $data['item_id']);
            if ($availableStock < $data['quantity']) {
                $itemName = $data['item_type'] === 'raw_material' 
                    ? ($item ? $item->raw_material_name : 'Item')
                    : ($item ? $item->product_name : 'Item');
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => "Insufficient stock available for {$itemName}. Available stock: " . number_format($availableStock, 3) . ". You cannot stock out more than available stock."]);
            }
        }

        // Get source document number
        $sourceDocumentNumber = null;
        if ($data['source_document_type'] && $data['source_document_id']) {
            if ($data['source_document_type'] === 'material_inward') {
                $sourceDoc = MaterialInward::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->inward_number : null;
            } elseif ($data['source_document_type'] === 'sales_invoice') {
                $sourceDoc = SalesInvoice::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->invoice_number : null;
            }
        }

        $stockTransaction = new StockTransaction();
        $stockTransaction->fill([
            'transaction_number' => $transactionNumber,
            'transaction_date' => $data['transaction_date'],
            'transaction_type' => $data['transaction_type'],
            'item_type' => $data['item_type'],
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
            'unit_of_measure' => $unitOfMeasure,
            'source_document_type' => $data['source_document_type'] ?? null,
            'source_document_id' => $data['source_document_id'] ?? null,
            'source_document_number' => $sourceDocumentNumber,
        ]);

        $user = Auth::user();
        if ($user) {
            $stockTransaction->organization_id = $user->organization_id ?? null;
            $stockTransaction->branch_id = session('active_branch_id');
            $stockTransaction->created_by = $user->id;
        }

        // Save the transaction (stock is calculated dynamically, not stored in raw_materials/products tables)
        $stockTransaction->save();

        return redirect()->route('stock-transactions.index')
            ->with('success', 'Stock Transaction created successfully.');
    }

    public function show(StockTransaction $stockTransaction)
    {
        return view('transactions.stock-transactions.show', compact('stockTransaction'));
    }

    public function edit(StockTransaction $stockTransaction)
    {
        $rawMaterials = RawMaterial::orderBy('raw_material_name')->get();
        $products = Product::orderBy('product_name')->get();
        $materialInwards = MaterialInward::orderByDesc('id')->get();
        $salesInvoices = SalesInvoice::orderByDesc('id')->get();

        return view('transactions.stock-transactions.edit', compact('stockTransaction', 'rawMaterials', 'products', 'materialInwards', 'salesInvoices'));
    }

    public function update(Request $request, StockTransaction $stockTransaction)
    {
        $data = $this->validateRequest($request, $stockTransaction);

        // Get item details
        $item = null;
        $unitOfMeasure = '';
        if ($data['item_type'] === 'raw_material') {
            $item = RawMaterial::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        } else {
            $item = Product::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        }

        $oldQuantity = $stockTransaction->quantity;
        $oldItemType = $stockTransaction->item_type;
        $oldItemId = $stockTransaction->item_id;
        $oldTransactionType = $stockTransaction->transaction_type;

        // Validate stock availability for stock out
        if ($data['transaction_type'] === 'stock_out') {
            // Calculate available stock after reversing old transaction
            $availableStock = $this->getAvailableStock($data['item_type'], $data['item_id']);
            
            // If old transaction was stock_out on same item, add back the old quantity
            if ($oldTransactionType === 'stock_out' && $oldItemType === $data['item_type'] && $oldItemId == $data['item_id']) {
                $availableStock += $oldQuantity;
            }
            // If old transaction was stock_in on same item, subtract the old quantity
            elseif ($oldTransactionType === 'stock_in' && $oldItemType === $data['item_type'] && $oldItemId == $data['item_id']) {
                $availableStock -= $oldQuantity;
            }
            
            if ($availableStock < $data['quantity']) {
                $itemName = $data['item_type'] === 'raw_material' 
                    ? ($item ? $item->raw_material_name : 'Item')
                    : ($item ? $item->product_name : 'Item');
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => "Insufficient stock available for {$itemName}. Available stock: " . number_format($availableStock, 3) . ". You cannot stock out more than available stock."]);
            }
        }

        // Get source document number
        $sourceDocumentNumber = null;
        if ($data['source_document_type'] && $data['source_document_id']) {
            if ($data['source_document_type'] === 'material_inward') {
                $sourceDoc = MaterialInward::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->inward_number : null;
            } elseif ($data['source_document_type'] === 'sales_invoice') {
                $sourceDoc = SalesInvoice::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->invoice_number : null;
            }
        }

        $stockTransaction->transaction_number = $data['transaction_number'] ?? $stockTransaction->transaction_number;
        $stockTransaction->transaction_date = $data['transaction_date'];
        $stockTransaction->transaction_type = $data['transaction_type'];
        $stockTransaction->item_type = $data['item_type'];
        $stockTransaction->item_id = $data['item_id'];
        $stockTransaction->quantity = $data['quantity'];
        $stockTransaction->unit_of_measure = $unitOfMeasure;
        $stockTransaction->source_document_type = $data['source_document_type'] ?? null;
        $stockTransaction->source_document_id = $data['source_document_id'] ?? null;
        $stockTransaction->source_document_number = $sourceDocumentNumber;

        // Save the transaction (stock is calculated dynamically, not stored in raw_materials/products tables)
        $stockTransaction->save();

        return redirect()->route('stock-transactions.index')
            ->with('success', 'Stock Transaction updated successfully.');
    }

    public function destroy(StockTransaction $stockTransaction)
    {
        // Delete the transaction (stock is calculated dynamically, not stored in raw_materials/products tables)
        $stockTransaction->delete();

        return redirect()->route('stock-transactions.index')
            ->with('success', 'Stock Transaction deleted successfully.');
    }

    protected function validateRequest(Request $request, ?StockTransaction $stockTransaction = null): array
    {
        $rules = [
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:stock_in,stock_out'],
            'item_type' => ['required', 'in:raw_material,product'],
            'item_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'source_document_type' => ['nullable', 'in:material_inward,sales_invoice'],
            'source_document_id' => ['nullable', 'integer'],
        ];

        if (!$stockTransaction) {
            $rules['transaction_number'] = ['nullable', 'string', 'max:191', 'unique:stock_transactions,transaction_number'];
        } else {
            $rules['transaction_number'] = ['nullable', 'string', 'max:191', 'unique:stock_transactions,transaction_number,' . $stockTransaction->id];
        }

        return $request->validate($rules);
    }

    protected function getAvailableStock(string $itemType, int $itemId): float
    {
        // Calculate stock dynamically based on Material Inward, Production, and Stock Transactions
        if ($itemType === 'raw_material') {
            $rawMaterial = RawMaterial::find($itemId);
            if (!$rawMaterial) {
                return 0;
            }
            
            // Get total received from Material Inward
            $totalReceived = MaterialInwardItem::where('raw_material_id', $itemId)
                ->sum('quantity_received') ?? 0;
            
            // Get total from stock transactions (stock_in - stock_out)
            $totalStockIn = StockTransaction::where('item_type', 'raw_material')
                ->where('item_id', $itemId)
                ->where('transaction_type', 'stock_in')
                ->sum('quantity') ?? 0;
            
            $totalStockOut = StockTransaction::where('item_type', 'raw_material')
                ->where('item_id', $itemId)
                ->where('transaction_type', 'stock_out')
                ->sum('quantity') ?? 0;
            
            // Calculate consumed from Production (through WorkOrderMaterials)
            $workOrderIdsWithProduction = Production::distinct()
                ->pluck('work_order_id')
                ->filter()
                ->toArray();
            
            $totalConsumed = \App\Models\WorkOrderMaterial::whereIn('work_order_id', $workOrderIdsWithProduction)
                ->where('raw_material_id', $itemId)
                ->get()
                ->sum(function($item) {
                    return $item->consumption ?? $item->material_required ?? 0;
                });
            
            // Available stock = Received + Stock In - Stock Out - Consumed
            return max(0, $totalReceived + $totalStockIn - $totalStockOut - $totalConsumed);
        } else {
            $product = Product::find($itemId);
            if (!$product) {
                return 0;
            }
            
            // Get total produced from Production
            $totalProduced = Production::where('product_id', $itemId)
                ->sum('produced_quantity') ?? 0;
            
            // Get total from stock transactions (stock_in - stock_out)
            $totalStockIn = StockTransaction::where('item_type', 'product')
                ->where('item_id', $itemId)
                ->where('transaction_type', 'stock_in')
                ->sum('quantity') ?? 0;
            
            $totalStockOut = StockTransaction::where('item_type', 'product')
                ->where('item_id', $itemId)
                ->where('transaction_type', 'stock_out')
                ->sum('quantity') ?? 0;
            
            // Get total sold from Sales Invoices
            $totalSold = SalesInvoiceItem::where('product_id', $itemId)
                ->sum('quantity_sold') ?? 0;
            
            // Available stock = Produced + Stock In - Stock Out - Sold
            return max(0, $totalProduced + $totalStockIn - $totalStockOut - $totalSold);
        }
    }
}

