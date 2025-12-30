<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalarySetup;
use App\Models\SalaryAdvance;
use App\Models\SalaryProcessing;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SalaryMasterApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Salary Setup API Methods
    public function salarySetupIndex(Request $request): JsonResponse
    {
        $query = SalarySetup::with(['employee']);
        
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('month')) {
            $query->where('salary_effective_from', '<=', $request->month)
                  ->where(function($q) use ($request) {
                      $q->whereNull('salary_effective_to')
                        ->orWhere('salary_effective_to', '>=', $request->month);
                  });
        }
        
        $salarySetups = $query->get();
        
        return response()->json(['data' => $salarySetups]);
    }

    public function salarySetupStore(Request $request): JsonResponse
    {
        // Validation and store logic (can delegate to main controller)
        return response()->json(['message' => 'Store method to be implemented'], 501);
    }

    public function salarySetupShow($id): JsonResponse
    {
        $salarySetup = SalarySetup::with(['employee'])->findOrFail($id);
        return response()->json(['data' => $salarySetup]);
    }

    public function salarySetupUpdate(Request $request, $id): JsonResponse
    {
        // Validation and update logic
        return response()->json(['message' => 'Update method to be implemented'], 501);
    }

    // Salary Advance API Methods
    public function salaryAdvanceIndex(Request $request): JsonResponse
    {
        $query = SalaryAdvance::with(['employee']);
        
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $salaryAdvances = $query->get();
        
        return response()->json(['data' => $salaryAdvances]);
    }

    public function salaryAdvanceStore(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Store method to be implemented'], 501);
    }

    public function salaryAdvanceShow($id): JsonResponse
    {
        $salaryAdvance = SalaryAdvance::with(['employee'])->findOrFail($id);
        return response()->json(['data' => $salaryAdvance]);
    }

    public function salaryAdvanceUpdate(Request $request, $id): JsonResponse
    {
        return response()->json(['message' => 'Update method to be implemented'], 501);
    }

    // Salary Processing API Methods
    public function salaryProcessingIndex(Request $request): JsonResponse
    {
        $query = SalaryProcessing::with(['employee', 'salaryAdvance']);
        
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->has('salary_month')) {
            $query->where('salary_month', $request->salary_month);
        }
        
        $salaryProcessings = $query->get();
        
        return response()->json(['data' => $salaryProcessings]);
    }

    public function salaryProcessingStore(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Store method to be implemented'], 501);
    }

    public function salaryProcessingShow($id): JsonResponse
    {
        $salaryProcessing = SalaryProcessing::with(['employee', 'salaryAdvance'])->findOrFail($id);
        return response()->json(['data' => $salaryProcessing]);
    }

    public function salaryProcessingUpdate(Request $request, $id): JsonResponse
    {
        return response()->json(['message' => 'Update method to be implemented'], 501);
    }

    public function markPaid(Request $request, $id): JsonResponse
    {
        return response()->json(['message' => 'Mark paid method to be implemented'], 501);
    }
}
