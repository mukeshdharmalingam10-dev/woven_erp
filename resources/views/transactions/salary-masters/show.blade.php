@extends('layouts.dashboard')

@section('title', 'View Salary Record - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Salary Record Details</h2>
        <div style="display: flex; gap: 10px;">
            @if($canWrite)
                <a href="{{ route('salary-masters.edit', $salaryMaster->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('salary-masters.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Basic Details -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Details</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Employee</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">{{ $salaryMaster->employee->employee_name ?? 'N/A' }}</p>
                <p style="color: #666; font-size: 14px; margin: 5px 0 0 0;">Code: {{ $salaryMaster->employee->code ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Salary Month</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">{{ \Carbon\Carbon::parse($salaryMaster->salary_month)->format('F Y') }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Monthly Salary Amount</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">₹{{ number_format($salaryMaster->monthly_salary_amount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Advance Details -->
    @if($salaryMaster->advance_applicable)
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Advance Details</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Advance Applicable</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">Yes</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Deduction Mode</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">{{ $salaryMaster->deduction_mode ?? 'N/A' }}</p>
            </div>
            
            @if($salaryMaster->deduction_mode === 'Full Deduction')
                <div>
                    <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Full Deduction Month</label>
                    <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">{{ $salaryMaster->full_deduction_month ? \Carbon\Carbon::parse($salaryMaster->full_deduction_month)->format('F Y') : 'N/A' }}</p>
                </div>
            @elseif($salaryMaster->deduction_mode === 'Monthly Installment')
                <div>
                    <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Installment Start Month</label>
                    <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">{{ $salaryMaster->installment_start_month ? \Carbon\Carbon::parse($salaryMaster->installment_start_month)->format('F Y') : 'N/A' }}</p>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Installment Amount</label>
                    <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">₹{{ number_format($salaryMaster->installment_amount ?? 0, 2) }}</p>
                </div>
            @elseif($salaryMaster->deduction_mode === 'Variable Installment')
                <div>
                    <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Variable Deduction Amount</label>
                    <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">₹{{ number_format($salaryMaster->variable_deduction_amount ?? 0, 2) }}</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Attendance Details -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Attendance Details</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Days Present</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">{{ $salaryMaster->days_present }} / {{ $salaryMaster->days_in_month }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Per Day Salary</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">₹{{ number_format($salaryMaster->per_day_salary, 2) }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Salary as per Attendance</label>
                <p style="color: #333; font-size: 16px; margin: 0; font-weight: 500;">₹{{ number_format($salaryMaster->salary_as_per_attendance, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div style="background: #e7f3ff; padding: 20px; border-radius: 5px; margin-bottom: 20px; border: 2px solid #667eea;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Summary</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Deduction Amount</label>
                <p style="color: #dc3545; font-size: 20px; margin: 0; font-weight: 600;">₹{{ number_format($salaryMaster->deduction_amount, 2) }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-weight: 500;">Net Salary</label>
                <p style="color: #28a745; font-size: 20px; margin: 0; font-weight: 600;">₹{{ number_format($salaryMaster->net_salary, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 14px; color: #666;">
            <div>
                <strong>Created By:</strong> {{ $salaryMaster->creator->name ?? 'N/A' }}<br>
                <strong>Created At:</strong> {{ $salaryMaster->created_at->format('d M Y, h:i A') }}
            </div>
            <div>
                <strong>Last Updated:</strong> {{ $salaryMaster->updated_at->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>
</div>
@endsection

