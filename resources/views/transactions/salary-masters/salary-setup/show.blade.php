@extends('layouts.dashboard')

@section('title', 'Salary Setup Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Salary Setup Details</h2>
        <div style="display: flex; gap: 10px;">
            @if($canWrite)
                <a href="{{ route('salary-masters.salary-setup.edit', $salarySetup->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('salary-masters.salary-setup.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Employee Salary Setup</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Employee</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salarySetup->employee->employee_name ?? 'N/A' }} ({{ $salarySetup->employee->code ?? 'N/A' }})</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Salary Type</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salarySetup->salary_type }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Monthly Salary Amount</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($salarySetup->monthly_salary_amount, 2) }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Status</label>
                <p style="margin: 0 0 20px 0;">
                    <span style="padding: 6px 12px; border-radius: 4px; font-size: 14px; font-weight: 500; background: {{ $salarySetup->status === 'Active' ? '#d4edda' : '#f8d7da' }}; color: {{ $salarySetup->status === 'Active' ? '#155724' : '#721c24' }};">
                        {{ $salarySetup->status }}
                    </span>
                </p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Salary Effective From</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salarySetup->salary_effective_from->format('M Y') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Salary Effective To</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $salarySetup->salary_effective_to ? $salarySetup->salary_effective_to->format('M Y') : 'Ongoing' }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created By</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $salarySetup->creator->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $salarySetup->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Last Updated</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $salarySetup->updated_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
