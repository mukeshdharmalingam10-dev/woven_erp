@extends('layouts.dashboard')

@section('title', 'Salary Processing Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Salary Processing Details</h2>
        <div style="display: flex; gap: 10px;">
            @if($canWrite && ($salaryProcessing->payment_status !== 'Paid' || auth()->user()->isAdmin()))
                <a href="{{ route('salary-masters.salary-processing.edit', $salaryProcessing->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('salary-masters.salary-processing.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Basic Information Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Employee</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 500;">
                    {{ $salaryProcessing->employee->employee_name ?? 'N/A' }} ({{ $salaryProcessing->employee->code ?? 'N/A' }})
                </p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Salary Month</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salaryProcessing->salary_month->format('F Y') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Monthly Salary Amount</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($salaryProcessing->monthly_salary_amount, 2) }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Payment Status</label>
                <p style="margin: 0 0 20px 0;">
                    <span style="padding: 6px 12px; border-radius: 4px; font-size: 14px; font-weight: 500; background: {{ $salaryProcessing->payment_status === 'Paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $salaryProcessing->payment_status === 'Paid' ? '#155724' : '#856404' }};">
                        {{ $salaryProcessing->payment_status }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Attendance Information Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Attendance Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Total Working Days</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salaryProcessing->total_working_days }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Present Days</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salaryProcessing->present_days }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Leave Days Deductible</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ number_format($salaryProcessing->leave_days_deductible, 2) }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Per Day Salary</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 500;">₹{{ number_format($salaryProcessing->per_day_salary, 2) }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Leave Deduction Amount</label>
                <p style="color: #dc3545; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($salaryProcessing->leave_deduction_amount, 2) }}</p>
            </div>
            @if($salaryProcessing->is_leave_overridden)
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Leave Override</label>
                    <p style="margin: 0 0 20px 0;">
                        <span style="padding: 6px 12px; border-radius: 4px; font-size: 14px; font-weight: 500; background: #fff3cd; color: #856404;">
                            Yes
                        </span>
                    </p>
                </div>
            @endif
        </div>
        @if($salaryProcessing->is_leave_overridden && $salaryProcessing->leave_override_reason)
            <div style="margin-top: 15px;">
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Leave Override Reason</label>
                <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; color: #333;">
                    {{ $salaryProcessing->leave_override_reason }}
                </div>
            </div>
        @endif
    </div>

    <!-- Advance Deduction Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Advance Deduction</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Advance Allocation Mode</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">
                    {{ $salaryProcessing->advance_allocation_mode === 'OLDEST_FIRST' ? 'Oldest First (Auto Allocate)' : 'Select Reference' }}
                </p>
            </div>
            @if($salaryProcessing->salaryAdvance)
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Advance Reference</label>
                    <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 500;">
                        {{ $salaryProcessing->salaryAdvance->advance_reference_no }}
                    </p>
                </div>
            @endif
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Advance Deduction Amount</label>
                <p style="color: #dc3545; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($salaryProcessing->advance_deduction_amount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Net Payable Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Net Payable</h3>
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Net Payable Salary</label>
            <p style="color: #333; font-size: 24px; margin: 0; font-weight: 700;">₹{{ number_format($salaryProcessing->net_payable_salary, 2) }}</p>
        </div>
    </div>

    <!-- Payment Information Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Payment Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Payment Status</label>
                <p style="margin: 0 0 20px 0;">
                    <span style="padding: 6px 12px; border-radius: 4px; font-size: 14px; font-weight: 500; background: {{ $salaryProcessing->payment_status === 'Paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $salaryProcessing->payment_status === 'Paid' ? '#155724' : '#856404' }};">
                        {{ $salaryProcessing->payment_status }}
                    </span>
                </p>
            </div>
            @if($salaryProcessing->paid_date)
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Paid Date</label>
                    <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salaryProcessing->paid_date->format('d M Y') }}</p>
                </div>
            @endif
            @if($salaryProcessing->notes)
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Notes</label>
                    <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; color: #333;">
                        {{ $salaryProcessing->notes }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Additional Information Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created By</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $salaryProcessing->creator->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $salaryProcessing->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Last Updated</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $salaryProcessing->updated_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

