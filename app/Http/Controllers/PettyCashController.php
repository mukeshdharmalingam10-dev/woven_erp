<?php

namespace App\Http\Controllers;

use App\Models\PettyCash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    public function index(Request $request)
    {
        $query = PettyCash::orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_id', 'like', "%{$search}%")
                    ->orWhere('paid_to', 'like', "%{$search}%")
                    ->orWhere('expense_category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $pettyCash = $query->paginate(15)->withQueryString();

        return view('transactions.petty-cash.index', compact('pettyCash'));
    }

    public function create()
    {
        return view('transactions.petty-cash.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $expenseId = $data['expense_id'] ?? ('PC-' . strtoupper(Str::random(8)));

        $pettyCash = new PettyCash();
        $pettyCash->fill([
            'expense_id' => $expenseId,
            'date' => $data['date'],
            'expense_category' => $data['expense_category'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'paid_to' => $data['paid_to'],
            'remarks' => $data['remarks'] ?? null,
        ]);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = 'petty-cash-' . time() . '-' . $file->getClientOriginalName();
            $path = $file->storeAs('petty-cash-receipts', $fileName, 'public');
            $pettyCash->receipt_path = $path;
        }

        $user = Auth::user();
        if ($user) {
            $pettyCash->organization_id = $user->organization_id ?? null;
            $pettyCash->branch_id = session('active_branch_id');
            $pettyCash->created_by = $user->id;
        }

        $pettyCash->save();

        return redirect()->route('petty-cash.index')
            ->with('success', 'Daily Expense entry created successfully.');
    }

    public function show(PettyCash $pettyCash)
    {
        return view('transactions.petty-cash.show', compact('pettyCash'));
    }

    public function edit(PettyCash $pettyCash)
    {
        return view('transactions.petty-cash.edit', compact('pettyCash'));
    }

    public function update(Request $request, PettyCash $pettyCash)
    {
        $data = $this->validateRequest($request, $pettyCash);

        $pettyCash->expense_id = $data['expense_id'] ?? $pettyCash->expense_id;
        $pettyCash->date = $data['date'];
        $pettyCash->expense_category = $data['expense_category'];
        $pettyCash->description = $data['description'] ?? null;
        $pettyCash->amount = $data['amount'];
        $pettyCash->payment_method = $data['payment_method'];
        $pettyCash->paid_to = $data['paid_to'];
        $pettyCash->remarks = $data['remarks'] ?? null;

        // Handle file upload
        if ($request->hasFile('receipt')) {
            // Delete old file if exists
            if ($pettyCash->receipt_path && Storage::disk('public')->exists($pettyCash->receipt_path)) {
                Storage::disk('public')->delete($pettyCash->receipt_path);
            }

            $file = $request->file('receipt');
            $fileName = 'petty-cash-' . time() . '-' . $file->getClientOriginalName();
            $path = $file->storeAs('petty-cash-receipts', $fileName, 'public');
            $pettyCash->receipt_path = $path;
        }

        $pettyCash->save();

        // Refresh the model to get updated data
        $pettyCash->refresh();

        return redirect()->route('petty-cash.edit', $pettyCash->id)
            ->with('success', 'Daily Expense entry updated successfully.');
    }

    public function deleteReceipt(PettyCash $pettyCash)
    {
        try {
            // Delete receipt file if exists
            if ($pettyCash->receipt_path && Storage::disk('public')->exists($pettyCash->receipt_path)) {
                Storage::disk('public')->delete($pettyCash->receipt_path);
            }
            
            // Clear receipt_path from database
            $pettyCash->receipt_path = null;
            $pettyCash->save();
            
            return response()->json(['success' => true, 'message' => 'Receipt deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting receipt: ' . $e->getMessage()], 500);
        }
    }

    public function showReceipt(PettyCash $pettyCash)
    {
        if (!$pettyCash->receipt_path) {
            abort(404, 'Receipt not found');
        }

        if (!Storage::disk('public')->exists($pettyCash->receipt_path)) {
            abort(404, 'Receipt file not found');
        }

        $filePath = Storage::disk('public')->path($pettyCash->receipt_path);
        $mimeType = Storage::disk('public')->mimeType($pettyCash->receipt_path);
        $lastModified = Storage::disk('public')->lastModified($pettyCash->receipt_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($pettyCash->receipt_path) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
            'ETag' => md5($pettyCash->receipt_path . $lastModified),
        ]);
    }

    public function destroy(PettyCash $pettyCash)
    {
        // Delete receipt file if exists
        if ($pettyCash->receipt_path && Storage::disk('public')->exists($pettyCash->receipt_path)) {
            Storage::disk('public')->delete($pettyCash->receipt_path);
        }

        $pettyCash->delete();

        return redirect()->route('petty-cash.index')
            ->with('success', 'Daily Expense entry deleted successfully.');
    }

    protected function validateRequest(Request $request, ?PettyCash $pettyCash = null): array
    {
        $rules = [
            'date' => ['required', 'date'],
            'expense_category' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:Cash,Credit,Debit'],
            'paid_to' => ['required', 'string', 'max:191'],
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'remarks' => ['nullable', 'string'],
        ];

        if (!$pettyCash) {
            $rules['expense_id'] = ['nullable', 'string', 'max:191', 'unique:daily_expenses,expense_id'];
        } else {
            $rules['expense_id'] = ['nullable', 'string', 'max:191', 'unique:daily_expenses,expense_id,' . $pettyCash->id];
        }

        return $request->validate($rules);
    }

    public function report(Request $request)
    {
        $query = PettyCash::orderBy('date', 'asc');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Expense category filter
        if ($request->filled('expense_category')) {
            $query->where('expense_category', $request->expense_category);
        }

        $pettyCashEntries = $query->get();

        // Calculate totals
        $totalAmount = $pettyCashEntries->sum('amount');
        
        // Group by category
        $categoryTotals = $pettyCashEntries->groupBy('expense_category')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        });

        // Get unique categories for dropdown
        $categories = PettyCash::distinct()->orderBy('expense_category')->pluck('expense_category');

        return view('transactions.petty-cash.report', compact(
            'pettyCashEntries',
            'totalAmount',
            'categoryTotals',
            'categories'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = PettyCash::orderBy('date', 'asc');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Expense category filter
        if ($request->filled('expense_category')) {
            $query->where('expense_category', $request->expense_category);
        }

        $pettyCashEntries = $query->get();
        $totalAmount = $pettyCashEntries->sum('amount');
        
        $categoryTotals = $pettyCashEntries->groupBy('expense_category')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        });

        $pdf = Pdf::loadView('transactions.petty-cash.export-pdf', compact(
            'pettyCashEntries',
            'totalAmount',
            'categoryTotals',
            'request'
        ));

        $filename = 'petty-cash-report-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $query = PettyCash::orderBy('date', 'asc');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Expense category filter
        if ($request->filled('expense_category')) {
            $query->where('expense_category', $request->expense_category);
        }

        $pettyCashEntries = $query->get();
        $totalAmount = $pettyCashEntries->sum('amount');

        // Generate CSV (simple Excel format)
        $filename = 'petty-cash-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($pettyCashEntries, $totalAmount) {
            $file = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'Expense ID',
                'Date',
                'Expense Category',
                'Description',
                'Amount',
                'Payment Method',
                'Paid To',
                'Remarks'
            ]);

            // Data rows
            foreach ($pettyCashEntries as $entry) {
                fputcsv($file, [
                    $entry->expense_id,
                    $entry->date ? $entry->date->format('Y-m-d') : '',
                    $entry->expense_category,
                    $entry->description ?? '',
                    number_format($entry->amount, 2),
                    $entry->payment_method,
                    $entry->paid_to,
                    $entry->remarks ?? '',
                ]);
            }

            // Total row
            fputcsv($file, []);
            fputcsv($file, ['Total Amount', '', '', '', number_format($totalAmount, 2), '', '', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
