<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        
        // Get all employees for the selected date
        $availableEmployees = Employee::orderBy('employee_name')
            ->get();

        // Get attendance records for the selected date
        $attendances = Attendance::whereDate('date', $selectedDate)
            ->with('employee')
            ->get()
            ->keyBy('employee_id');

        // Calculate statistics
        $totalAvailable = $availableEmployees->count();
        $totalPresent = $attendances->where('status', 'Present')->count();
        $totalAbsent = $attendances->where('status', 'Absent')->count();

        // Get absentees (employee IDs)
        $absenteeIds = $attendances->where('status', 'Absent')->pluck('employee_id')->toArray();

        return view('attendance.index', compact(
            'selectedDate',
            'availableEmployees',
            'attendances',
            'totalAvailable',
            'totalPresent',
            'totalAbsent',
            'absenteeIds'
        ));
    }

    public function create()
    {
        $selectedDate = request()->get('date', now()->format('Y-m-d'));
        
        // Get all employees
        $availableEmployees = Employee::orderBy('employee_name')
            ->get();

        // Get existing attendance for the date
        $existingAttendance = Attendance::whereDate('date', $selectedDate)
            ->pluck('status', 'employee_id')
            ->toArray();

        $absenteeIds = [];
        foreach ($existingAttendance as $employeeId => $status) {
            if ($status === 'Absent') {
                $absenteeIds[] = $employeeId;
            }
        }

        return view('attendance.create', compact(
            'selectedDate',
            'availableEmployees',
            'absenteeIds'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'absentees' => ['nullable', 'array'],
            'absentees.*' => ['exists:employees,id'],
        ], [
            'date.before_or_equal' => 'You cannot mark attendance for future dates.',
        ]);

        $date = $request->date;
        $absenteeIds = $request->absentees ?? [];

        // Get all employees
        $allEmployees = Employee::pluck('id');

        $user = Auth::user();
        $organizationId = $user->organization_id ?? null;
        $branchId = session('active_branch_id');
        $createdBy = $user->id;

        // Use updateOrCreate to handle unique constraint properly
        // First, permanently delete any soft-deleted records for this date to avoid unique constraint issues
        Attendance::withTrashed()
            ->whereDate('date', $date)
            ->forceDelete();

        // Then use updateOrCreate to update existing records or create new ones
        DB::transaction(function () use ($date, $allEmployees, $absenteeIds, $organizationId, $branchId, $createdBy) {
            foreach ($allEmployees as $employeeId) {
                $status = in_array($employeeId, $absenteeIds) ? 'Absent' : 'Present';
                
                Attendance::updateOrCreate(
                    [
                        'date' => $date,
                        'employee_id' => $employeeId,
                    ],
                    [
                        'status' => $status,
                        'organization_id' => $organizationId,
                        'branch_id' => $branchId,
                        'created_by' => $createdBy,
                    ]
                );
            }
        });

        return redirect()->route('attendances.index', ['date' => $date])
            ->with('success', 'Attendance recorded successfully.');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load('employee');
        return view('attendance.show', compact('attendance'));
    }

    public function edit($date)
    {
        $selectedDate = $date;
        
        // Get all employees
        $availableEmployees = Employee::orderBy('employee_name')
            ->get();

        // Get existing attendance for the date
        $existingAttendance = Attendance::whereDate('date', $selectedDate)
            ->pluck('status', 'employee_id')
            ->toArray();

        $absenteeIds = [];
        foreach ($existingAttendance as $employeeId => $status) {
            if ($status === 'Absent') {
                $absenteeIds[] = $employeeId;
            }
        }

        return view('attendance.edit', compact(
            'selectedDate',
            'availableEmployees',
            'absenteeIds'
        ));
    }

    public function update(Request $request, $date)
    {
        $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'absentees' => ['nullable', 'array'],
            'absentees.*' => ['exists:employees,id'],
        ], [
            'date.before_or_equal' => 'You cannot mark attendance for future dates.',
        ]);

        $absenteeIds = $request->absentees ?? [];

        // Get all employees
        $allEmployees = Employee::pluck('id');

        $user = Auth::user();
        $organizationId = $user->organization_id ?? null;
        $branchId = session('active_branch_id');
        $createdBy = $user->id;

        // Use updateOrCreate to handle unique constraint properly
        // First, permanently delete any soft-deleted records for this date to avoid unique constraint issues
        Attendance::withTrashed()
            ->whereDate('date', $date)
            ->forceDelete();

        // Then use updateOrCreate to update existing records or create new ones
        DB::transaction(function () use ($date, $allEmployees, $absenteeIds, $organizationId, $branchId, $createdBy) {
            foreach ($allEmployees as $employeeId) {
                $status = in_array($employeeId, $absenteeIds) ? 'Absent' : 'Present';
                
                Attendance::updateOrCreate(
                    [
                        'date' => $date,
                        'employee_id' => $employeeId,
                    ],
                    [
                        'status' => $status,
                        'organization_id' => $organizationId,
                        'branch_id' => $branchId,
                        'created_by' => $createdBy,
                    ]
                );
            }
        });

        return redirect()->route('attendances.index', ['date' => $date])
            ->with('success', 'Attendance updated successfully.');
    }

    public function destroy($date)
    {
        Attendance::whereDate('date', $date)->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance deleted successfully.');
    }

    public function report(Request $request)
    {
        $query = Attendance::with('employee')->orderBy('date', 'desc');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Employee filter
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->get();

        // Get all employees for dropdown
        $employees = Employee::orderBy('employee_name')->get();

        // Calculate statistics
        $totalRecords = $attendances->count();
        $totalPresent = $attendances->where('status', 'Present')->count();
        $totalAbsent = $attendances->where('status', 'Absent')->count();

        return view('attendance.report', compact(
            'attendances',
            'employees',
            'totalRecords',
            'totalPresent',
            'totalAbsent'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = Attendance::with('employee')->orderBy('date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->get();
        $totalPresent = $attendances->where('status', 'Present')->count();
        $totalAbsent = $attendances->where('status', 'Absent')->count();

        $pdf = Pdf::loadView('attendance.export-pdf', compact(
            'attendances',
            'totalPresent',
            'totalAbsent',
            'request'
        ));

        $filename = 'attendance-report-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $query = Attendance::with('employee')->orderBy('date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->get();
        $totalPresent = $attendances->where('status', 'Present')->count();
        $totalAbsent = $attendances->where('status', 'Absent')->count();

        $filename = 'attendance-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances, $totalPresent, $totalAbsent) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Date',
                'Employee Name',
                'Employee Code',
                'Department',
                'Status'
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->date ? $attendance->date->format('Y-m-d') : '',
                    $attendance->employee->employee_name ?? '',
                    $attendance->employee->code ?? '',
                    $attendance->employee->department ?? '',
                    $attendance->status,
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['Total Present', $totalPresent]);
            fputcsv($file, ['Total Absent', $totalAbsent]);
            fputcsv($file, ['Total Records', $attendances->count()]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
