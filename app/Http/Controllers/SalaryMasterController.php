<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\SalarySetup;
use App\Models\SalaryAdvance;
use App\Models\SalaryProcessing;
use App\Models\SalaryAdvanceDeductionMap;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryMasterController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('auth');
    }

    // ==================== Main Index Method ====================

    public function index(Request $request)
    {
        $this->checkReadPermission('salary-masters');
        
        $search = $request->input('search', '');
        
        // Get Salary Setups
        $setupQuery = SalarySetup::with(['employee'])->orderByDesc('id');
        $branchId = session('active_branch_id');
        if ($branchId) {
            $setupQuery->where('branch_id', $branchId);
        }
        if ($search) {
            $setupQuery->whereHas('employee', function($q) use ($search) {
                $q->where('employee_name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        $salarySetups = $setupQuery->paginate(5, ['*'], 'setup_page')->withQueryString();
        
        // Get Salary Advances
        $advanceQuery = SalaryAdvance::with(['employee'])->orderByDesc('id');
        if ($branchId) {
            $advanceQuery->where('branch_id', $branchId);
        }
        if ($search) {
            $advanceQuery->where(function($q) use ($search) {
                $q->where('advance_reference_no', 'like', '%' . $search . '%')
                  ->orWhereHas('employee', function($qe) use ($search) {
                      $qe->where('employee_name', 'like', '%' . $search . '%')
                         ->orWhere('code', 'like', '%' . $search . '%');
                  });
            });
        }
        $salaryAdvances = $advanceQuery->paginate(5, ['*'], 'advance_page')->withQueryString();
        
        // Calculate total deducted for each advance
        $salaryAdvances->getCollection()->transform(function($advance) {
            $totalDeducted = SalaryProcessing::where('salary_advance_id', $advance->id)
                ->sum('advance_deduction_amount');
            $advance->total_deducted = $totalDeducted;
            return $advance;
        });
        
        // Get Salary Processings
        $processingQuery = SalaryProcessing::with(['employee', 'salaryAdvance'])->orderByDesc('salary_month')->orderByDesc('id');
        if ($branchId) {
            $processingQuery->where('branch_id', $branchId);
        }
        if ($search) {
            $processingQuery->whereHas('employee', function($q) use ($search) {
                $q->where('employee_name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        $salaryProcessings = $processingQuery->paginate(5, ['*'], 'processing_page')->withQueryString();
        
        $permissions = $this->getPermissionFlags('salary-masters');
        
        return view('transactions.salary-masters.index', compact('salarySetups', 'salaryAdvances', 'salaryProcessings', 'search') + $permissions);
    }

    // ==================== Salary Setup Methods ====================

    public function salarySetupIndex(Request $request)
    {
        $this->checkReadPermission('salary-masters');
        
        $search = $request->input('search');
        $query = SalarySetup::with(['employee'])->orderByDesc('id');
        
        // Apply branch filter
        $branchId = session('active_branch_id');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('employee_name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        
        $salarySetups = $query->paginate(15)->withQueryString();
        $permissions = $this->getPermissionFlags('salary-masters');
        
        return view('transactions.salary-masters.salary-setup.index', compact('salarySetups', 'search') + $permissions);
    }

    public function salarySetupCreate()
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        
        return view('transactions.salary-masters.salary-setup.create', compact('employees'));
    }

    public function salarySetupStore(Request $request)
    {
        $this->checkWritePermission('salary-masters');
        
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'monthly_salary_amount' => 'required|numeric|min:0',
            'salary_effective_from' => 'required|date',
            'salary_effective_to' => 'nullable|date|after:salary_effective_from',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Validate overlap: Only one ACTIVE salary setup should apply for a given employee + salary month
        if ($request->status === 'Active') {
            $effectiveFrom = Carbon::parse($request->salary_effective_from)->startOfMonth();
            $effectiveTo = $request->salary_effective_to ? Carbon::parse($request->salary_effective_to)->endOfMonth() : null;

            $overlappingSetups = SalarySetup::where('employee_id', $request->employee_id)
                ->where('status', 'Active')
                ->where(function($query) use ($effectiveFrom, $effectiveTo) {
                    $query->where(function($q) use ($effectiveFrom, $effectiveTo) {
                        // New setup starts within existing setup range
                        $q->where('salary_effective_from', '<=', $effectiveFrom)
                          ->where(function($q2) use ($effectiveFrom) {
                              $q2->whereNull('salary_effective_to')
                                 ->orWhere('salary_effective_to', '>=', $effectiveFrom);
                          });
                    })->orWhere(function($q) use ($effectiveFrom, $effectiveTo) {
                        // New setup ends within existing setup range
                        if ($effectiveTo) {
                            $q->where('salary_effective_from', '<=', $effectiveTo)
                              ->where(function($q2) use ($effectiveTo) {
                                  $q2->whereNull('salary_effective_to')
                                     ->orWhere('salary_effective_to', '>=', $effectiveTo);
                              });
                        }
                    })->orWhere(function($q) use ($effectiveFrom, $effectiveTo) {
                        // New setup completely encompasses existing setup
                        if ($effectiveTo) {
                            $q->where('salary_effective_from', '>=', $effectiveFrom)
                              ->where(function($q2) use ($effectiveTo) {
                                  $q2->whereNull('salary_effective_to')
                                     ->orWhere('salary_effective_to', '<=', $effectiveTo);
                              });
                        }
                    });
                })
                ->exists();

            if ($overlappingSetups) {
                return back()->withErrors(['salary_effective_from' => 'An active salary setup already exists for this employee that overlaps with the specified date range. Only one active salary setup should apply for a given employee at any time.'])->withInput();
            }
        }
        
        $user = Auth::user();
        SalarySetup::create([
            'employee_id' => $request->employee_id,
            'salary_type' => 'Monthly',
            'monthly_salary_amount' => $request->monthly_salary_amount,
            'salary_effective_from' => $request->salary_effective_from,
            'salary_effective_to' => $request->salary_effective_to,
            'status' => $request->status,
            'organization_id' => $user->organization_id,
            'branch_id' => session('active_branch_id'),
            'created_by' => $user->id,
        ]);
        
        return redirect()->route('salary-masters.salary-setup.index')
            ->with('success', 'Salary setup created successfully.');
    }

    public function salarySetupShow(SalarySetup $salarySetup)
    {
        $this->checkReadPermission('salary-masters');
        $permissions = $this->getPermissionFlags('salary-masters');
        
        $salarySetup->load(['employee', 'organization', 'branch', 'creator']);
        
        return view('transactions.salary-masters.salary-setup.show', compact('salarySetup') + $permissions);
    }

    public function salarySetupEdit(SalarySetup $salarySetup)
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        
        return view('transactions.salary-masters.salary-setup.edit', compact('salarySetup', 'employees'));
    }

    public function salarySetupUpdate(Request $request, SalarySetup $salarySetup)
    {
        $this->checkWritePermission('salary-masters');
        
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'monthly_salary_amount' => 'required|numeric|min:0',
            'salary_effective_from' => 'required|date',
            'salary_effective_to' => 'nullable|date|after:salary_effective_from',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Validate overlap: Only one ACTIVE salary setup should apply for a given employee + salary month
        if ($request->status === 'Active') {
            $effectiveFrom = Carbon::parse($request->salary_effective_from)->startOfMonth();
            $effectiveTo = $request->salary_effective_to ? Carbon::parse($request->salary_effective_to)->endOfMonth() : null;

            $overlappingSetups = SalarySetup::where('employee_id', $request->employee_id)
                ->where('status', 'Active')
                ->where('id', '!=', $salarySetup->id)
                ->where(function($query) use ($effectiveFrom, $effectiveTo) {
                    $query->where(function($q) use ($effectiveFrom, $effectiveTo) {
                        $q->where('salary_effective_from', '<=', $effectiveFrom)
                          ->where(function($q2) use ($effectiveFrom) {
                              $q2->whereNull('salary_effective_to')
                                 ->orWhere('salary_effective_to', '>=', $effectiveFrom);
                          });
                    })->orWhere(function($q) use ($effectiveFrom, $effectiveTo) {
                        if ($effectiveTo) {
                            $q->where('salary_effective_from', '<=', $effectiveTo)
                              ->where(function($q2) use ($effectiveTo) {
                                  $q2->whereNull('salary_effective_to')
                                     ->orWhere('salary_effective_to', '>=', $effectiveTo);
                              });
                        }
                    })->orWhere(function($q) use ($effectiveFrom, $effectiveTo) {
                        if ($effectiveTo) {
                            $q->where('salary_effective_from', '>=', $effectiveFrom)
                              ->where(function($q2) use ($effectiveTo) {
                                  $q2->whereNull('salary_effective_to')
                                     ->orWhere('salary_effective_to', '<=', $effectiveTo);
                              });
                        }
                    });
                })
                ->exists();

            if ($overlappingSetups) {
                return back()->withErrors(['salary_effective_from' => 'An active salary setup already exists for this employee that overlaps with the specified date range. Only one active salary setup should apply for a given employee at any time.'])->withInput();
            }
        }
        
        $salarySetup->update($request->only([
            'employee_id',
            'monthly_salary_amount',
            'salary_effective_from',
            'salary_effective_to',
            'status',
        ]));
        
        return redirect()->route('salary-masters.salary-setup.index')
            ->with('success', 'Salary setup updated successfully.');
    }

    public function salarySetupDestroy(SalarySetup $salarySetup)
    {
        $this->checkDeletePermission('salary-masters');
        
        $salarySetup->delete();
        
        return redirect()->route('salary-masters.salary-setup.index')
            ->with('success', 'Salary setup deleted successfully.');
    }

    // ==================== Salary Advance Methods ====================

    public function salaryAdvanceIndex(Request $request)
    {
        $this->checkReadPermission('salary-masters');
        
        $search = $request->input('search');
        $query = SalaryAdvance::with(['employee'])->orderByDesc('id');
        
        // Apply branch filter
        $branchId = session('active_branch_id');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('advance_reference_no', 'like', '%' . $search . '%')
                  ->orWhereHas('employee', function($qe) use ($search) {
                      $qe->where('employee_name', 'like', '%' . $search . '%')
                         ->orWhere('code', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $salaryAdvances = $query->paginate(15)->withQueryString();
        
        // Calculate total deducted for each advance
        $salaryAdvances->getCollection()->transform(function($advance) {
            $totalDeducted = SalaryProcessing::where('salary_advance_id', $advance->id)
                ->sum('advance_deduction_amount');
            $advance->total_deducted = $totalDeducted;
            return $advance;
        });
        
        $permissions = $this->getPermissionFlags('salary-masters');
        
        return view('transactions.salary-masters.salary-advance.index', compact('salaryAdvances', 'search') + $permissions);
    }

    public function salaryAdvanceCreate()
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        
        return view('transactions.salary-masters.salary-advance.create', compact('employees'));
    }

    public function salaryAdvanceStore(Request $request)
    {
        $this->checkWritePermission('salary-masters');
        
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'advance_date' => 'required|date',
            'advance_amount' => 'required|numeric|min:0.01',
            'advance_deduction_mode' => 'required|in:Full Deduction,Monthly Installment,Variable Installment',
            'installment_start_month' => 'required_if:advance_deduction_mode,Monthly Installment,Variable Installment|nullable|date',
            'installment_amount' => 'required_if:advance_deduction_mode,Monthly Installment|nullable|numeric|min:0.01',
            'remarks' => 'nullable|string|max:1000',
        ]);
        
        $user = Auth::user();
        SalaryAdvance::create([
            'employee_id' => $request->employee_id,
            'advance_date' => $request->advance_date,
            'advance_amount' => $request->advance_amount,
            'advance_deduction_mode' => $request->advance_deduction_mode,
            'installment_start_month' => $request->installment_start_month,
            'installment_amount' => $request->installment_amount,
            'remarks' => $request->remarks,
            'total_deducted_amount' => 0,
            'advance_balance_amount' => $request->advance_amount, // Initially balance equals amount
            'status' => 'OPEN',
            'organization_id' => $user->organization_id,
            'branch_id' => session('active_branch_id'),
            'created_by' => $user->id,
        ]);
        
        return redirect()->route('salary-masters.salary-advance.index')
            ->with('success', 'Salary advance created successfully.');
    }

    public function salaryAdvanceShow(SalaryAdvance $salaryAdvance)
    {
        $this->checkReadPermission('salary-masters');
        $permissions = $this->getPermissionFlags('salary-masters');
        
        $salaryAdvance->load(['employee', 'organization', 'branch', 'creator']);
        
        $totalDeducted = SalaryProcessing::where('salary_advance_id', $salaryAdvance->id)
            ->sum('advance_deduction_amount');
        
        return view('transactions.salary-masters.salary-advance.show', compact('salaryAdvance', 'totalDeducted') + $permissions);
    }

    public function salaryAdvanceEdit(SalaryAdvance $salaryAdvance)
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        
        return view('transactions.salary-masters.salary-advance.edit', compact('salaryAdvance', 'employees'));
    }

    public function salaryAdvanceUpdate(Request $request, SalaryAdvance $salaryAdvance)
    {
        $this->checkWritePermission('salary-masters');
        
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'advance_date' => 'required|date',
            'advance_amount' => 'required|numeric|min:0.01',
            'advance_deduction_mode' => 'required|in:Full Deduction,Monthly Installment,Variable Installment',
            'installment_start_month' => 'required_if:advance_deduction_mode,Monthly Installment,Variable Installment|nullable|date',
            'installment_amount' => 'required_if:advance_deduction_mode,Monthly Installment|nullable|numeric|min:0.01',
            'remarks' => 'nullable|string|max:1000',
        ]);
        
        $salaryAdvance->update($request->only([
            'employee_id',
            'advance_date',
            'advance_amount',
            'advance_deduction_mode',
            'installment_start_month',
            'installment_amount',
            'remarks',
        ]));
        
        return redirect()->route('salary-masters.salary-advance.index')
            ->with('success', 'Salary advance updated successfully.');
    }

    public function salaryAdvanceDestroy(SalaryAdvance $salaryAdvance)
    {
        $this->checkDeletePermission('salary-masters');
        
        $salaryAdvance->delete();
        
        return redirect()->route('salary-masters.salary-advance.index')
            ->with('success', 'Salary advance deleted successfully.');
    }

    // ==================== Salary Processing Methods ====================

    public function salaryProcessingIndex(Request $request)
    {
        $this->checkReadPermission('salary-masters');
        
        $search = $request->input('search');
        $query = SalaryProcessing::with(['employee', 'salaryAdvance'])->orderByDesc('salary_month')->orderByDesc('id');
        
        // Apply branch filter
        $branchId = session('active_branch_id');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('employee_name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        
        $salaryProcessings = $query->paginate(15)->withQueryString();
        $permissions = $this->getPermissionFlags('salary-masters');
        
        return view('transactions.salary-masters.salary-processing.index', compact('salaryProcessings', 'search') + $permissions);
    }

    public function salaryProcessingCreate()
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        
        return view('transactions.salary-masters.salary-processing.create', compact('employees'));
    }

    public function salaryProcessingStore(Request $request)
    {
        $this->checkWritePermission('salary-masters');
        
        $request->validate([
            'salary_month' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'leave_days_deductible' => 'nullable|numeric|min:0',
            'is_leave_overridden' => 'nullable|boolean',
            'leave_override_reason' => 'required_if:is_leave_overridden,1|nullable|string|max:1000',
            'advance_deduction_amount' => 'required|numeric|min:0',
            'advance_allocation_mode' => 'required|in:SELECT_REFERENCE,OLDEST_FIRST',
            'salary_advance_id' => 'nullable|exists:salary_advances,id|required_if:advance_allocation_mode,SELECT_REFERENCE',
            'payment_status' => 'required|in:Pending,Paid',
            'paid_date' => 'required_if:payment_status,Paid|nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Get employee's active salary setup with effective date range validation
        $salaryMonthDate = Carbon::parse($request->salary_month)->startOfMonth();
        $salarySetup = SalarySetup::where('employee_id', $request->employee_id)
            ->where('status', 'Active')
            ->where('salary_effective_from', '<=', $salaryMonthDate)
            ->where(function($q) use ($salaryMonthDate) {
                $q->whereNull('salary_effective_to')
                  ->orWhere('salary_effective_to', '>=', $salaryMonthDate);
            })
            ->first();
        
        if (!$salarySetup) {
            return back()->withErrors(['employee_id' => 'No active salary setup found for this employee for the selected month. Please create an active salary setup with effective date range covering ' . $salaryMonthDate->format('M Y') . '.'])->withInput();
        }
        
        // Check for duplicate processing
        $existingProcessing = SalaryProcessing::where('employee_id', $request->employee_id)
            ->where('salary_month', $salaryMonthDate)
            ->first();
        
        if ($existingProcessing) {
            return back()->withErrors(['salary_month' => 'Salary processing already exists for this employee for the selected month.'])->withInput();
        }
        
        // Calculate total working days
        $totalWorkingDays = $this->calculateWorkingDays($salaryMonthDate);
        
        // Get attendance data
        $attendanceData = $this->getAttendanceData($request->employee_id, $salaryMonthDate);
        $presentDays = $attendanceData['present_days'];
        $actualLeaveDays = $attendanceData['leave_days'];
        
        // Handle leave override
        $isLeaveOverridden = $request->is_leave_overridden ?? false;
        $leaveDaysDeductible = $isLeaveOverridden ? ($request->leave_days_deductible ?? $actualLeaveDays) : $actualLeaveDays;
        $leaveOverrideReason = $isLeaveOverridden ? ($request->leave_override_reason ?? null) : null;
        
        if ($isLeaveOverridden && empty($leaveOverrideReason)) {
            return back()->withErrors(['leave_override_reason' => 'Leave override reason is required when leave days are overridden.'])->withInput();
        }
        
        // Calculate per day salary and leave deduction
        $perDaySalary = $salarySetup->monthly_salary_amount / $totalWorkingDays;
        $leaveDeductionAmount = $leaveDaysDeductible * $perDaySalary;
        
        // Handle advance deduction
        $advanceDeductionAmount = $request->advance_deduction_amount ?? 0;
        $advanceAllocationMode = $request->advance_allocation_mode ?? 'OLDEST_FIRST';
        $advanceReferenceId = null;
        
        if ($advanceDeductionAmount > 0) {
            // Validate advance deduction amount doesn't exceed maximum deductible
            $maxDeductible = max(0, $salarySetup->monthly_salary_amount - $leaveDeductionAmount);
            if ($advanceDeductionAmount > $maxDeductible) {
                return back()->withErrors(['advance_deduction_amount' => 'Advance deduction cannot exceed maximum deductible amount of ' . number_format($maxDeductible, 2) . ' after leave deduction.'])->withInput();
            }
            
            if ($advanceAllocationMode === 'SELECT_REFERENCE') {
                if (!$request->salary_advance_id) {
                    return back()->withErrors(['salary_advance_id' => 'Advance reference is required when using SELECT_REFERENCE allocation mode.'])->withInput();
                }
                $advanceReferenceId = $request->salary_advance_id;
                $advance = SalaryAdvance::find($advanceReferenceId);
                if (!$advance || $advance->employee_id != $request->employee_id) {
                    return back()->withErrors(['salary_advance_id' => 'Invalid advance reference selected.'])->withInput();
                }
                $availableBalance = $advance->advance_amount - ($advance->total_deducted_amount ?? 0);
                if ($advanceDeductionAmount > $availableBalance) {
                    return back()->withErrors(['advance_deduction_amount' => 'Advance deduction amount cannot exceed available balance of ' . number_format($availableBalance, 2) . ' for the selected advance.'])->withInput();
                }
            } else {
                // OLDEST_FIRST - validate total available balance across all advances
                $totalAvailableBalance = SalaryAdvance::where('employee_id', $request->employee_id)
                    ->where('status', 'OPEN')
                    ->get()
                    ->sum(function($advance) {
                        return $advance->advance_amount - ($advance->total_deducted_amount ?? 0);
                    });
                if ($advanceDeductionAmount > $totalAvailableBalance) {
                    return back()->withErrors(['advance_deduction_amount' => 'Advance deduction amount cannot exceed total available balance of ' . number_format($totalAvailableBalance, 2) . ' across all open advances.'])->withInput();
                }
            }
        }
        
        // Calculate net payable salary
        $netPayableSalary = max(0, $salarySetup->monthly_salary_amount - $leaveDeductionAmount - $advanceDeductionAmount);
        
        $user = Auth::user();
        $salaryProcessing = SalaryProcessing::create([
            'salary_month' => $salaryMonthDate,
            'employee_id' => $request->employee_id,
            'monthly_salary_amount' => $salarySetup->monthly_salary_amount,
            'attendance_source_month' => $salaryMonthDate,
            'total_working_days' => $totalWorkingDays,
            'present_days' => $presentDays,
            'leave_days' => $actualLeaveDays,
            'leave_days_deductible' => $leaveDaysDeductible,
            'per_day_salary' => $perDaySalary,
            'leave_deduction_amount' => $leaveDeductionAmount,
            'leave_override_reason' => $leaveOverrideReason,
            'is_leave_overridden' => $isLeaveOverridden,
            'advance_deduction_amount' => $advanceDeductionAmount,
            'advance_allocation_mode' => $advanceAllocationMode,
            'net_payable_salary' => $netPayableSalary,
            'salary_advance_id' => $advanceReferenceId,
            'payment_status' => $request->payment_status,
            'paid_date' => $request->paid_date,
            'notes' => $request->notes,
            'organization_id' => $user->organization_id,
            'branch_id' => session('active_branch_id'),
            'created_by' => $user->id,
        ]);
        
        // If marked as PAID, post deductions
        if ($request->payment_status === 'Paid' && $advanceDeductionAmount > 0) {
            $this->postAdvanceDeductions($salaryProcessing, $advanceAllocationMode, $advanceDeductionAmount, $advanceReferenceId);
        }
        
        return redirect()->route('salary-masters.salary-processing.index')
            ->with('success', 'Salary processing created successfully.');
    }

    public function salaryProcessingShow(SalaryProcessing $salaryProcessing)
    {
        $this->checkReadPermission('salary-masters');
        $permissions = $this->getPermissionFlags('salary-masters');
        
        $salaryProcessing->load(['employee', 'salaryAdvance', 'organization', 'branch', 'creator']);
        
        return view('transactions.salary-masters.salary-processing.show', compact('salaryProcessing') + $permissions);
    }

    public function salaryProcessingEdit(SalaryProcessing $salaryProcessing)
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        
        // Get pending advances for the employee
        $pendingAdvances = SalaryAdvance::where('employee_id', $salaryProcessing->employee_id)
            ->where('advance_balance_amount', '>', 0)
            ->orderBy('advance_date')
            ->get();
        
        return view('transactions.salary-masters.salary-processing.edit', compact('salaryProcessing', 'employees', 'pendingAdvances'));
    }

    public function salaryProcessingUpdate(Request $request, SalaryProcessing $salaryProcessing)
    {
        $this->checkWritePermission('salary-masters');
        
        // Block edits to paid records unless admin
        if ($salaryProcessing->payment_status === 'Paid' && !Auth::user()->isAdmin()) {
            return back()->withErrors(['payment_status' => 'Cannot edit salary processing record that is already marked as Paid. Only admin users can edit paid records.'])->withInput();
        }
        
        $request->validate([
            'leave_days_deductible' => 'nullable|numeric|min:0',
            'is_leave_overridden' => 'nullable|boolean',
            'leave_override_reason' => 'required_if:is_leave_overridden,1|nullable|string|max:1000',
            'advance_deduction_amount' => 'required|numeric|min:0',
            'advance_allocation_mode' => 'required|in:SELECT_REFERENCE,OLDEST_FIRST',
            'salary_advance_id' => 'nullable|exists:salary_advances,id|required_if:advance_allocation_mode,SELECT_REFERENCE',
            'payment_status' => 'required|in:Pending,Paid',
            'paid_date' => 'required_if:payment_status,Paid|nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $monthlySalaryAmount = $salaryProcessing->monthly_salary_amount;
        $totalWorkingDays = $salaryProcessing->total_working_days;
        $perDaySalary = $monthlySalaryAmount / $totalWorkingDays;
        
        // Handle leave override
        $isLeaveOverridden = $request->is_leave_overridden ?? false;
        $leaveDaysDeductible = $isLeaveOverridden ? ($request->leave_days_deductible ?? $salaryProcessing->leave_days) : $salaryProcessing->leave_days;
        $leaveOverrideReason = $isLeaveOverridden ? ($request->leave_override_reason ?? null) : null;
        
        if ($isLeaveOverridden && empty($leaveOverrideReason)) {
            return back()->withErrors(['leave_override_reason' => 'Leave override reason is required when leave days are overridden.'])->withInput();
        }
        
        $leaveDeductionAmount = $leaveDaysDeductible * $perDaySalary;
        
        // Handle advance deduction
        $advanceDeductionAmount = $request->advance_deduction_amount ?? 0;
        $advanceAllocationMode = $request->advance_allocation_mode ?? 'OLDEST_FIRST';
        $advanceReferenceId = null;
        
        if ($advanceDeductionAmount > 0) {
            // Validate advance deduction amount doesn't exceed maximum deductible
            $maxDeductible = max(0, $monthlySalaryAmount - $leaveDeductionAmount);
            if ($advanceDeductionAmount > $maxDeductible) {
                return back()->withErrors(['advance_deduction_amount' => 'Advance deduction cannot exceed maximum deductible amount of ' . number_format($maxDeductible, 2) . ' after leave deduction.'])->withInput();
            }
            
            if ($advanceAllocationMode === 'SELECT_REFERENCE') {
                if (!$request->salary_advance_id) {
                    return back()->withErrors(['salary_advance_id' => 'Advance reference is required when using SELECT_REFERENCE allocation mode.'])->withInput();
                }
                $advanceReferenceId = $request->salary_advance_id;
                $advance = SalaryAdvance::find($advanceReferenceId);
                if (!$advance || $advance->employee_id != $salaryProcessing->employee_id) {
                    return back()->withErrors(['salary_advance_id' => 'Invalid advance reference selected.'])->withInput();
                }
                // Exclude current processing's deductions
                $totalDeductedForThisProcessing = SalaryAdvanceDeductionMap::where('advance_id', $advanceReferenceId)
                    ->where('salary_processing_id', $salaryProcessing->id)
                    ->sum('deducted_amount');
                $availableBalance = $advance->advance_amount - ($advance->total_deducted_amount ?? 0) + $totalDeductedForThisProcessing;
                if ($advanceDeductionAmount > $availableBalance) {
                    return back()->withErrors(['advance_deduction_amount' => 'Advance deduction amount cannot exceed available balance of ' . number_format($availableBalance, 2) . ' for the selected advance.'])->withInput();
                }
            } else {
                // OLDEST_FIRST - validate total available balance
                $totalAvailableBalance = SalaryAdvance::where('employee_id', $salaryProcessing->employee_id)
                    ->where('status', 'OPEN')
                    ->get()
                    ->sum(function($advance) use ($salaryProcessing) {
                        $totalDeductedForThisProcessing = SalaryAdvanceDeductionMap::where('advance_id', $advance->id)
                            ->where('salary_processing_id', $salaryProcessing->id)
                            ->sum('deducted_amount');
                        return ($advance->advance_amount - ($advance->total_deducted_amount ?? 0) + $totalDeductedForThisProcessing);
                    });
                if ($advanceDeductionAmount > $totalAvailableBalance) {
                    return back()->withErrors(['advance_deduction_amount' => 'Advance deduction amount cannot exceed total available balance of ' . number_format($totalAvailableBalance, 2) . ' across all open advances.'])->withInput();
                }
            }
        }
        
        // Calculate net payable salary
        $netPayableSalary = max(0, $monthlySalaryAmount - $leaveDeductionAmount - $advanceDeductionAmount);
        
        // If previously paid, reverse old deductions
        $wasPaid = $salaryProcessing->payment_status === 'Paid';
        if ($wasPaid) {
            // Delete old deduction maps and recalculate advance balances
            $oldMaps = SalaryAdvanceDeductionMap::where('salary_processing_id', $salaryProcessing->id)->get();
            foreach ($oldMaps as $map) {
                SalaryAdvanceDeductionMap::where('id', $map->id)->delete();
                $this->updateAdvanceBalance($map->advance_id);
            }
        }
        
        $salaryProcessing->update([
            'leave_days_deductible' => $leaveDaysDeductible,
            'per_day_salary' => $perDaySalary,
            'leave_deduction_amount' => $leaveDeductionAmount,
            'leave_override_reason' => $leaveOverrideReason,
            'is_leave_overridden' => $isLeaveOverridden,
            'advance_deduction_amount' => $advanceDeductionAmount,
            'advance_allocation_mode' => $advanceAllocationMode,
            'net_payable_salary' => $netPayableSalary,
            'salary_advance_id' => $advanceReferenceId,
            'payment_status' => $request->payment_status,
            'paid_date' => $request->paid_date,
            'notes' => $request->notes,
        ]);
        
        // If marked as PAID, post deductions
        if ($request->payment_status === 'Paid' && $advanceDeductionAmount > 0) {
            $this->postAdvanceDeductions($salaryProcessing, $advanceAllocationMode, $advanceDeductionAmount, $advanceReferenceId);
        }
        
        return redirect()->route('salary-masters.salary-processing.index')
            ->with('success', 'Salary processing updated successfully.');
    }

    public function salaryProcessingDestroy(SalaryProcessing $salaryProcessing)
    {
        $this->checkDeletePermission('salary-masters');
        
        // Block deletion of paid records unless admin
        if ($salaryProcessing->payment_status === 'Paid' && !Auth::user()->isAdmin()) {
            return back()->withErrors(['payment_status' => 'Cannot delete salary processing record that is already marked as Paid. Only admin users can delete paid records.']);
        }
        
        // Reverse deductions if it was paid
        if ($salaryProcessing->payment_status === 'Paid') {
            $oldMaps = SalaryAdvanceDeductionMap::where('salary_processing_id', $salaryProcessing->id)->get();
            foreach ($oldMaps as $map) {
                SalaryAdvanceDeductionMap::where('id', $map->id)->delete();
                $this->updateAdvanceBalance($map->advance_id);
            }
        }
        
        $salaryProcessing->delete();
        
        return redirect()->route('salary-masters.salary-processing.index')
            ->with('success', 'Salary processing deleted successfully.');
    }

    /**
     * Mark salary processing as paid
     */
    public function markPaid(Request $request, SalaryProcessing $salaryProcessing)
    {
        $this->checkWritePermission('salary-masters');
        
        $request->validate([
            'paid_date' => 'required|date',
        ]);
        
        if ($salaryProcessing->payment_status === 'Paid') {
            return back()->withErrors(['payment_status' => 'Salary processing is already marked as Paid.']);
        }
        
        // Post advance deductions
        if ($salaryProcessing->advance_deduction_amount > 0) {
            $this->postAdvanceDeductions(
                $salaryProcessing,
                $salaryProcessing->advance_allocation_mode,
                $salaryProcessing->advance_deduction_amount,
                $salaryProcessing->salary_advance_id
            );
        }
        
        $salaryProcessing->update([
            'payment_status' => 'Paid',
            'paid_date' => $request->paid_date,
        ]);
        
        return redirect()->route('salary-masters.salary-processing.index')
            ->with('success', 'Salary processing marked as paid successfully.');
    }

    // ==================== AJAX Helper Methods ====================

    public function getEmployeeSalarySetup(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $salaryMonth = $request->get('salary_month');
        
        if (!$employeeId || !$salaryMonth) {
            return response()->json(['error' => 'Employee ID and Salary Month are required.'], 400);
        }
        
        $salarySetup = SalarySetup::where('employee_id', $employeeId)
            ->where('status', 'Active')
            ->where('salary_effective_from', '<=', $salaryMonth)
            ->where(function($q) use ($salaryMonth) {
                $q->whereNull('salary_effective_to')
                  ->orWhere('salary_effective_to', '>=', $salaryMonth);
            })
            ->first();
        
        if (!$salarySetup) {
            return response()->json(['error' => 'No active salary setup found for this employee.'], 400);
        }
        
        // Get attendance data
        $attendanceData = $this->getAttendanceData($employeeId, $salaryMonth);
        $totalWorkingDays = $this->calculateWorkingDays(Carbon::parse($salaryMonth));
        $perDaySalary = $salarySetup->monthly_salary_amount / $totalWorkingDays;
        
        // Get pending advances
        $pendingAdvances = SalaryAdvance::where('employee_id', $employeeId)
            ->where('advance_balance_amount', '>', 0)
            ->orderBy('advance_date')
            ->get()
            ->map(function($advance) {
                return [
                    'id' => $advance->id,
                    'advance_reference_no' => $advance->advance_reference_no,
                    'advance_date' => $advance->advance_date->format('Y-m-d'),
                    'advance_amount' => number_format($advance->advance_amount, 2),
                    'advance_balance_amount' => number_format($advance->advance_balance_amount, 2),
                    'advance_balance_raw' => $advance->advance_balance_amount,
                    'advance_deduction_mode' => $advance->advance_deduction_mode,
                ];
            });
        
        return response()->json([
            'monthly_salary_amount' => number_format($salarySetup->monthly_salary_amount, 2),
            'monthly_salary_amount_raw' => $salarySetup->monthly_salary_amount,
            'total_working_days' => $totalWorkingDays,
            'present_days' => $attendanceData['present_days'],
            'leave_days' => $attendanceData['leave_days'],
            'per_day_salary' => number_format($perDaySalary, 2),
            'per_day_salary_raw' => $perDaySalary,
            'leave_deduction_amount' => number_format($attendanceData['leave_days'] * $perDaySalary, 2),
            'pending_advances' => $pendingAdvances,
        ]);
    }

    // ==================== Private Helper Methods ====================

    private function calculateWorkingDays(Carbon $month)
    {
        // Default to 30 days, can be configured based on company policy
        // This can be enhanced to exclude weekends and holidays
        return 30;
    }

    private function getAttendanceData($employeeId, $salaryMonth)
    {
        $startDate = Carbon::parse($salaryMonth)->startOfMonth();
        $endDate = Carbon::parse($salaryMonth)->endOfMonth();
        
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        
        $presentDays = $attendances->where('status', 'Present')->count();
        $leaveDays = $attendances->where('status', 'Absent')->count();
        
        return [
            'present_days' => $presentDays,
            'leave_days' => $leaveDays,
        ];
    }

    private function updateAdvanceBalance($advanceId)
    {
        $advance = SalaryAdvance::find($advanceId);
        if ($advance) {
            // Calculate total deducted from deduction maps (more accurate)
            $totalDeducted = SalaryAdvanceDeductionMap::where('advance_id', $advanceId)
                ->sum('deducted_amount');
            
            $advance->total_deducted_amount = $totalDeducted;
            $advance->advance_balance_amount = $advance->advance_amount - $totalDeducted;
            
            // Update status to CLOSED if balance is 0
            if ($advance->advance_balance_amount <= 0) {
                $advance->status = 'CLOSED';
            } else {
                $advance->status = 'OPEN';
            }
            
            $advance->save();
        }
    }

    /**
     * Post advance deductions to salary_advance_deduction_map
     */
    private function postAdvanceDeductions(SalaryProcessing $salaryProcessing, $allocationMode, $totalDeductionAmount, $selectedAdvanceId = null)
    {
        $user = Auth::user();
        
        if ($allocationMode === 'SELECT_REFERENCE' && $selectedAdvanceId) {
            // Single advance deduction
            SalaryAdvanceDeductionMap::create([
                'salary_processing_id' => $salaryProcessing->id,
                'advance_id' => $selectedAdvanceId,
                'deducted_amount' => $totalDeductionAmount,
                'created_by' => $user->id,
            ]);
            $this->updateAdvanceBalance($selectedAdvanceId);
        } else {
            // OLDEST_FIRST: Allocate across oldest OPEN advances
            $advances = SalaryAdvance::where('employee_id', $salaryProcessing->employee_id)
                ->where('status', 'OPEN')
                ->orderBy('advance_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();
            
            $remainingAmount = $totalDeductionAmount;
            
            foreach ($advances as $advance) {
                if ($remainingAmount <= 0) {
                    break;
                }
                
                $availableBalance = $advance->advance_amount - ($advance->total_deducted_amount ?? 0);
                $deductAmount = min($remainingAmount, $availableBalance);
                
                if ($deductAmount > 0) {
                    SalaryAdvanceDeductionMap::create([
                        'salary_processing_id' => $salaryProcessing->id,
                        'advance_id' => $advance->id,
                        'deducted_amount' => $deductAmount,
                        'created_by' => $user->id,
                    ]);
                    $this->updateAdvanceBalance($advance->id);
                    $remainingAmount -= $deductAmount;
                }
            }
        }
    }
}