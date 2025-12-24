<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        $query = Product::query();
        
        // Filter by organization/branch if needed
        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        if ($user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('unit_of_measure', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        
        switch ($sortBy) {
            case 'product_name':
                $query->orderBy('product_name', $sortOrder);
                break;
            case 'code':
                $query->orderBy('code', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            default:
                $query->orderBy('id', $sortOrder);
                break;
        }
        
        $products = $query->paginate(15)->withQueryString();
        
        return view('masters.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('masters.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'description' => 'nullable|string',
        ], [
            'product_name.required' => 'Product Name is required.',
            'product_name.max' => 'Product Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
        ]);

        // Generate sequential Product ID starting from PR001
        $allProducts = Product::withTrashed()
            ->where('code', 'like', 'PR%')
            ->get();

        $maxNumber = 0;
        foreach ($allProducts as $product) {
            // Extract number from code (e.g., PR001 -> 1, PR123 -> 123)
            if (preg_match('/^PR(\d+)$/i', $product->code, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Next number is max + 1, starting from 1 if no products exist
        $nextNumber = $maxNumber + 1;

        // Format as PR001, PR002, etc. (3 digits with leading zeros)
        $code = 'PR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Safety check: if code exists (shouldn't happen, but just in case), find next available
        $maxAttempts = 10000;
        $attempts = 0;
        while (Product::withTrashed()->where('code', $code)->exists() && $attempts < $maxAttempts) {
            $nextNumber++;
            $code = 'PR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }

        $user = auth()->user();
        
        $product = Product::create([
            'product_name' => $request->product_name,
            'code' => $code,
            'unit_of_measure' => $request->unit_of_measure,
            'description' => $request->description,
            'organization_id' => $user->organization_id,
            'branch_id' => $user->branch_id,
            'created_by' => $user->id,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('masters.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('masters.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'description' => 'nullable|string',
        ], [
            'product_name.required' => 'Product Name is required.',
            'product_name.max' => 'Product Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
        ]);

        // Product ID (code) is not editable - it remains the same
        $product->update([
            'product_name' => $request->product_name,
            'unit_of_measure' => $request->unit_of_measure,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
