<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\SalaryMaster;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalaryMasterController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->checkReadPermission('salary-masters');
        
        $user = auth()->user();
        $query = SalaryMaster::with(['employee'])->orderByDesc('salary_month')->orderByDesc('id');
        
        // Filter by organization/branch if needed
        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        $branchId = session('active_branch_id');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('employee', function($qe) use ($search) {
                    $qe->where('employee_name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }
        
        // Filter by employee
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        
        // Filter by month
        if ($request->has('salary_month') && $request->salary_month) {
            $query->whereYear('salary_month', Carbon::parse($request->salary_month)->year)
                  ->whereMonth('salary_month', Carbon::parse($request->salary_month)->month);
        }
        
        $salaryMasters = $query->paginate(15)->withQueryString();
        
        $employees = Employee::orderBy('employee_name')->get();
        $permissions = $this->getPermissionFlags('salary-masters');
        
        return view('transactions.salary-masters.index', compact('salaryMasters', 'employees') + $permissions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        
        return view('transactions.salary-masters.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkWritePermission('salary-masters');
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|date',
            'monthly_salary_amount' => 'required|numeric|min:0.01',
            'advance_applicable' => 'required|boolean',
            'deduction_mode' => 'required_if:advance_applicable,1|in:Full Deduction,Monthly Installment,Variable Installment',
            'full_deduction_month' => 'required_if:deduction_mode,Full Deduction|nullable|date',
            'full_deduction_amount' => 'required_if:deduction_mode,Full Deduction|nullable|numeric|min:0.01',
            'installment_start_month' => 'required_if:deduction_mode,Monthly Installment|nullable|date',
            'installment_amount' => 'required_if:deduction_mode,Monthly Installment|nullable|numeric|min:0.01',
            'variable_deduction_amount' => 'required_if:deduction_mode,Variable Installment|nullable|numeric|min:0.01',
        ], [
            'employee_id.required' => 'Employee is required.',
            'salary_month.required' => 'Salary Month is required.',
            'monthly_salary_amount.required' => 'Monthly Salary Amount is required.',
            'monthly_salary_amount.min' => 'Monthly Salary Amount must be greater than 0.',
            'advance_applicable.required' => 'Advance Applicable is required.',
            'deduction_mode.required_if' => 'Deduction Mode is required when Advance is applicable.',
            'full_deduction_month.required_if' => 'Full Deduction Month is required for Full Deduction mode.',
            'full_deduction_amount.required_if' => 'Full Deduction Amount is required for Full Deduction mode.',
            'installment_start_month.required_if' => 'Installment Start Month is required for Monthly Installment mode.',
            'installment_amount.required_if' => 'Installment Amount is required for Monthly Installment mode.',
            'variable_deduction_amount.required_if' => 'Deduction Amount is required for Variable Installment mode.',
        ]);

        // Check for duplicate salary record for same employee and month
        $salaryMonth = Carbon::parse($request->salary_month)->startOfMonth();
        $existing = SalaryMaster::where('employee_id', $request->employee_id)
            ->whereYear('salary_month', $salaryMonth->year)
            ->whereMonth('salary_month', $salaryMonth->month)
            ->first();
            
        if ($existing) {
            return back()->withErrors(['salary_month' => 'Salary record already exists for this employee and month.'])->withInput();
        }

        // Calculate attendance and salary
        $calculations = $this->calculateSalary($request);
        
        if (isset($calculations['error'])) {
            return back()->withErrors(['calculation' => $calculations['error']])->withInput();
        }

        $user = Auth::user();
        
        SalaryMaster::create([
            'employee_id' => $request->employee_id,
            'salary_month' => $salaryMonth,
            'monthly_salary_amount' => $request->monthly_salary_amount,
            'advance_applicable' => $request->advance_applicable,
            'deduction_mode' => $request->advance_applicable ? $request->deduction_mode : null,
            'full_deduction_month' => $request->deduction_mode === 'Full Deduction' ? Carbon::parse($request->full_deduction_month)->startOfMonth() : null,
            'full_deduction_amount' => $request->deduction_mode === 'Full Deduction' ? $request->full_deduction_amount : null,
            'installment_start_month' => $request->deduction_mode === 'Monthly Installment' ? Carbon::parse($request->installment_start_month)->startOfMonth() : null,
            'installment_amount' => $request->deduction_mode === 'Monthly Installment' ? $request->installment_amount : null,
            'variable_deduction_amount' => $request->deduction_mode === 'Variable Installment' ? $request->variable_deduction_amount : null,
            'days_present' => $calculations['days_present'],
            'days_in_month' => $calculations['days_in_month'],
            'absent_days' => $calculations['absent_days'],
            'per_day_salary' => $calculations['per_day_salary'],
            'salary_as_per_attendance' => $calculations['salary_as_per_attendance'],
            'deduction_amount' => $calculations['deduction_amount'],
            'net_salary' => $calculations['net_salary'],
            'organization_id' => $user->organization_id,
            'branch_id' => session('active_branch_id'),
            'created_by' => $user->id,
        ]);

        return redirect()->route('salary-masters.index')
            ->with('success', 'Salary record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalaryMaster $salaryMaster): View
    {
        $this->checkReadPermission('salary-masters');
        $permissions = $this->getPermissionFlags('salary-masters');
        
        $salaryMaster->load(['employee', 'creator']);
        
        return view('transactions.salary-masters.show', compact('salaryMaster') + $permissions);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalaryMaster $salaryMaster): View
    {
        $this->checkWritePermission('salary-masters');
        
        $employees = Employee::orderBy('employee_name')->get();
        $salaryMaster->load('employee');
        
        return view('transactions.salary-masters.edit', compact('salaryMaster', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalaryMaster $salaryMaster): RedirectResponse
    {
        $this->checkWritePermission('salary-masters');
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|date',
            'monthly_salary_amount' => 'required|numeric|min:0.01',
            'advance_applicable' => 'required|boolean',
            'deduction_mode' => 'required_if:advance_applicable,1|in:Full Deduction,Monthly Installment,Variable Installment',
            'full_deduction_month' => 'required_if:deduction_mode,Full Deduction|nullable|date',
            'full_deduction_amount' => 'required_if:deduction_mode,Full Deduction|nullable|numeric|min:0.01',
            'installment_start_month' => 'required_if:deduction_mode,Monthly Installment|nullable|date',
            'installment_amount' => 'required_if:deduction_mode,Monthly Installment|nullable|numeric|min:0.01',
            'variable_deduction_amount' => 'required_if:deduction_mode,Variable Installment|nullable|numeric|min:0.01',
        ], [
            'employee_id.required' => 'Employee is required.',
            'salary_month.required' => 'Salary Month is required.',
            'monthly_salary_amount.required' => 'Monthly Salary Amount is required.',
            'monthly_salary_amount.min' => 'Monthly Salary Amount must be greater than 0.',
            'advance_applicable.required' => 'Advance Applicable is required.',
            'deduction_mode.required_if' => 'Deduction Mode is required when Advance is applicable.',
            'full_deduction_month.required_if' => 'Full Deduction Month is required for Full Deduction mode.',
            'full_deduction_amount.required_if' => 'Full Deduction Amount is required for Full Deduction mode.',
            'installment_start_month.required_if' => 'Installment Start Month is required for Monthly Installment mode.',
            'installment_amount.required_if' => 'Installment Amount is required for Monthly Installment mode.',
            'variable_deduction_amount.required_if' => 'Deduction Amount is required for Variable Installment mode.',
        ]);

        // Check for duplicate salary record for same employee and month (excluding current record)
        $salaryMonth = Carbon::parse($request->salary_month)->startOfMonth();
        $existing = SalaryMaster::where('employee_id', $request->employee_id)
            ->whereYear('salary_month', $salaryMonth->year)
            ->whereMonth('salary_month', $salaryMonth->month)
            ->where('id', '!=', $salaryMaster->id)
            ->first();
            
        if ($existing) {
            return back()->withErrors(['salary_month' => 'Salary record already exists for this employee and month.'])->withInput();
        }

        // Calculate attendance and salary
        $calculations = $this->calculateSalary($request);
        
        if (isset($calculations['error'])) {
            return back()->withErrors(['calculation' => $calculations['error']])->withInput();
        }

        $salaryMaster->update([
            'employee_id' => $request->employee_id,
            'salary_month' => $salaryMonth,
            'monthly_salary_amount' => $request->monthly_salary_amount,
            'advance_applicable' => $request->advance_applicable,
            'deduction_mode' => $request->advance_applicable ? $request->deduction_mode : null,
            'full_deduction_month' => $request->deduction_mode === 'Full Deduction' ? Carbon::parse($request->full_deduction_month)->startOfMonth() : null,
            'full_deduction_amount' => $request->deduction_mode === 'Full Deduction' ? $request->full_deduction_amount : null,
            'installment_start_month' => $request->deduction_mode === 'Monthly Installment' ? Carbon::parse($request->installment_start_month)->startOfMonth() : null,
            'installment_amount' => $request->deduction_mode === 'Monthly Installment' ? $request->installment_amount : null,
            'variable_deduction_amount' => $request->deduction_mode === 'Variable Installment' ? $request->variable_deduction_amount : null,
            'days_present' => $calculations['days_present'],
            'days_in_month' => $calculations['days_in_month'],
            'absent_days' => $calculations['absent_days'],
            'per_day_salary' => $calculations['per_day_salary'],
            'salary_as_per_attendance' => $calculations['salary_as_per_attendance'],
            'deduction_amount' => $calculations['deduction_amount'],
            'net_salary' => $calculations['net_salary'],
        ]);

        return redirect()->route('salary-masters.index')
            ->with('success', 'Salary record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryMaster $salaryMaster): RedirectResponse
    {
        $this->checkDeletePermission('salary-masters');
        
        $salaryMaster->delete();

        return redirect()->route('salary-masters.index')
            ->with('success', 'Salary record deleted successfully.');
    }

    /**
     * Get attendance data for employee and month (AJAX)
     */
    public function getAttendance(Request $request)
    {
        try {
            $employeeId = $request->get('employee_id');
            $salaryMonth = $request->get('salary_month');
            
            if (!$employeeId || !$salaryMonth) {
                return response()->json(['error' => 'Employee and Salary Month are required.'], 400);
            }

            $month = Carbon::parse($salaryMonth);
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
            
            // Get days present (status = 'Present')
            $daysPresent = Attendance::where('employee_id', $employeeId)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'Present')
                ->count();
            
            $daysInMonth = $startDate->daysInMonth;
            $absentDays = $daysInMonth - $daysPresent;
            
            return response()->json([
                'days_present' => $daysPresent,
                'days_in_month' => $daysInMonth,
                'absent_days' => $absentDays,
                'attendance_available' => $daysPresent > 0 || Attendance::where('employee_id', $employeeId)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->exists(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching attendance: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading attendance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calculate salary based on attendance and deductions
     */
    private function calculateSalary(Request $request): array
    {
        $employeeId = $request->employee_id;
        $salaryMonth = Carbon::parse($request->salary_month);
        $monthlySalary = $request->monthly_salary_amount;
        
        $startDate = $salaryMonth->copy()->startOfMonth();
        $endDate = $salaryMonth->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;
        
        // Get days present
        $daysPresent = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'Present')
            ->count();
        
        $absentDays = $daysInMonth - $daysPresent;
        
        // Calculate per day salary
        $perDaySalary = $daysInMonth > 0 ? $monthlySalary / $daysInMonth : 0;
        
        // Calculate salary as per attendance
        $salaryAsPerAttendance = max(0, $monthlySalary - ($absentDays * $perDaySalary));
        
        // Calculate deduction amount
        $deductionAmount = 0;
        
        if ($request->advance_applicable) {
            $deductionMode = $request->deduction_mode;
            
            if ($deductionMode === 'Full Deduction') {
                $fullDeductionMonth = Carbon::parse($request->full_deduction_month)->startOfMonth();
                if ($salaryMonth->format('Y-m') === $fullDeductionMonth->format('Y-m')) {
                    $deductionAmount = $request->full_deduction_amount ?? 0;
                    // Cap by salary as per attendance
                    $deductionAmount = min($deductionAmount, $salaryAsPerAttendance);
                }
            } elseif ($deductionMode === 'Monthly Installment') {
                $installmentStartMonth = Carbon::parse($request->installment_start_month)->startOfMonth();
                if ($salaryMonth->greaterThanOrEqualTo($installmentStartMonth)) {
                    $deductionAmount = $request->installment_amount ?? 0;
                    // Cap by salary as per attendance
                    $deductionAmount = min($deductionAmount, $salaryAsPerAttendance);
                }
            } elseif ($deductionMode === 'Variable Installment') {
                $deductionAmount = $request->variable_deduction_amount ?? 0;
                // Cap by salary as per attendance
                $deductionAmount = min($deductionAmount, $salaryAsPerAttendance);
            }
        }
        
        // Validate deduction doesn't exceed salary
        if ($deductionAmount > $salaryAsPerAttendance) {
            return ['error' => 'Deduction cannot exceed salary payable for the month.'];
        }
        
        // Calculate net salary
        $netSalary = max(0, $salaryAsPerAttendance - $deductionAmount);
        
        return [
            'days_present' => $daysPresent,
            'days_in_month' => $daysInMonth,
            'absent_days' => $absentDays,
            'per_day_salary' => round($perDaySalary, 2),
            'salary_as_per_attendance' => round($salaryAsPerAttendance, 2),
            'deduction_amount' => round($deductionAmount, 2),
            'net_salary' => round($netSalary, 2),
        ];
    }
}

