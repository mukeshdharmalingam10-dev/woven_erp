<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RawMaterialController extends Controller
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
        
        $query = RawMaterial::query();
        
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
                $q->where('raw_material_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('unit_of_measure', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        
        switch ($sortBy) {
            case 'raw_material_name':
                $query->orderBy('raw_material_name', $sortOrder);
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
        
        $rawMaterials = $query->paginate(15)->withQueryString();
        
        return view('masters.raw-materials.index', compact('rawMaterials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('masters.raw-materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'raw_material_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'description' => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
        ], [
            'raw_material_name.required' => 'Raw Material Name is required.',
            'raw_material_name.max' => 'Raw Material Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
            'reorder_level.integer' => 'Reorder Level must be a whole number.',
            'reorder_level.min' => 'Reorder Level must be at least 0.',
        ]);

        // Generate sequential Raw Material ID starting from RM001
        $allRawMaterials = RawMaterial::withTrashed()
            ->where('code', 'like', 'RM%')
            ->get();

        $maxNumber = 0;
        foreach ($allRawMaterials as $rawMaterial) {
            // Extract number from code (e.g., RM001 -> 1, RM123 -> 123)
            if (preg_match('/^RM(\d+)$/i', $rawMaterial->code, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Next number is max + 1, starting from 1 if no raw materials exist
        $nextNumber = $maxNumber + 1;
        
        // Format as RM001, RM002, etc. (3 digits with leading zeros)
        $code = 'RM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Safety check: if code exists (shouldn't happen, but just in case), find next available
        $maxAttempts = 10000;
        $attempts = 0;
        while (RawMaterial::withTrashed()->where('code', $code)->exists() && $attempts < $maxAttempts) {
            $nextNumber++;
            $code = 'RM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }

        $user = auth()->user();
        
                $rawMaterial = RawMaterial::create([
                    'raw_material_name' => $request->raw_material_name,
                    'code' => $code,
                    'unit_of_measure' => $request->unit_of_measure,
                    'description' => $request->description,
                    'reorder_level' => $request->reorder_level ?? 0,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                    'created_by' => $user->id,
                ]);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Raw Material created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RawMaterial $rawMaterial): View
    {
        return view('masters.raw-materials.show', compact('rawMaterial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RawMaterial $rawMaterial): View
    {
        return view('masters.raw-materials.edit', compact('rawMaterial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $request->validate([
            'raw_material_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'description' => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
        ], [
            'raw_material_name.required' => 'Raw Material Name is required.',
            'raw_material_name.max' => 'Raw Material Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
            'reorder_level.integer' => 'Reorder Level must be a whole number.',
            'reorder_level.min' => 'Reorder Level must be at least 0.',
        ]);

        // Raw Material ID (code) is not editable - it remains the same
        $rawMaterial->update([
            'raw_material_name' => $request->raw_material_name,
            'unit_of_measure' => $request->unit_of_measure,
            'description' => $request->description,
            'reorder_level' => $request->reorder_level ?? $rawMaterial->reorder_level ?? 0,
        ]);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Raw Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RawMaterial $rawMaterial): RedirectResponse
    {
        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')
            ->with('success', 'Raw Material deleted successfully.');
    }
}
