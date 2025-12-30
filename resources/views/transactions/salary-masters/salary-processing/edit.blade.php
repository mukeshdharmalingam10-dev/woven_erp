@extends('layouts.dashboard')

@section('title', 'Edit Salary Processing - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $isPaid = $salaryProcessing->payment_status === 'Paid';
    $canEdit = $canWrite && (!$isPaid || $user->isAdmin());
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Salary Processing</h2>
        <a href="{{ route('salary-masters.salary-processing.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($isPaid && !$user->isAdmin())
        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffc107;">
            <strong>Notice:</strong> This salary processing record is marked as Paid and cannot be edited unless you are an admin.
        </div>
    @endif

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('salary-masters.salary-processing.update', $salaryProcessing->id) }}" method="POST" id="salaryProcessingForm">
        @csrf
        @method('PUT')

        <!-- Basic Information Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="salary_month" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Salary Month</label>
                    <input type="month" name="salary_month" id="salary_month" value="{{ old('salary_month', $salaryProcessing->salary_month->format('Y-m')) }}" {{ !$canEdit ? 'readonly' : 'required' }}
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                    @error('salary_month')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="employee_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee</label>
                    <select name="employee_id" id="employee_id" {{ !$canEdit ? 'disabled' : 'required' }}
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $salaryProcessing->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_name }} ({{ $employee->code }})
                            </option>
                        @endforeach
                    </select>
                    @if(!$canEdit)
                        <input type="hidden" name="employee_id" value="{{ $salaryProcessing->employee_id }}">
                    @endif
                    @error('employee_id')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="monthly_salary_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Monthly Salary Amount</label>
                <input type="text" id="monthly_salary_amount" readonly
                    value="₹{{ number_format($salaryProcessing->monthly_salary_amount, 2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
            </div>
        </div>

        <!-- Attendance Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Attendance Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Total Working Days</label>
                    <input type="text" id="total_working_days" readonly value="{{ $salaryProcessing->total_working_days }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Present Days</label>
                    <input type="text" id="present_days" readonly value="{{ $salaryProcessing->present_days }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>

                <div>
                    <label for="leave_days_deductible" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Leave Days Deductible</label>
                    <input type="number" step="0.01" min="0" name="leave_days_deductible" id="leave_days_deductible" 
                        value="{{ old('leave_days_deductible', $salaryProcessing->leave_days_deductible) }}"
                        {{ !$canEdit ? 'readonly' : '' }}
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                    @error('leave_days_deductible')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Per Day Salary</label>
                    <input type="text" id="per_day_salary" readonly value="₹{{ number_format($salaryProcessing->per_day_salary, 2) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Leave Deduction Amount</label>
                    <input type="text" id="leave_deduction_amount" readonly value="₹{{ number_format($salaryProcessing->leave_deduction_amount, 2) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>
            </div>

            @if($canEdit)
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; color: #333; font-weight: 500;">
                        <input type="checkbox" name="is_leave_overridden" id="is_leave_overridden" value="1" {{ old('is_leave_overridden', $salaryProcessing->is_leave_overridden) ? 'checked' : '' }}>
                        Override Leave Days
                    </label>
                    <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Check this to manually override leave days</small>
                </div>

                <div id="leave_override_reason_field" style="display: {{ old('is_leave_overridden', $salaryProcessing->is_leave_overridden) ? 'block' : 'none' }}; margin-bottom: 20px;">
                    <label for="leave_override_reason" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Leave Override Reason</label>
                    <textarea name="leave_override_reason" id="leave_override_reason" rows="3"
                              placeholder="Enter reason for overriding leave days"
                              style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('leave_override_reason', $salaryProcessing->leave_override_reason) }}</textarea>
                    @error('leave_override_reason')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            @else
                @if($salaryProcessing->is_leave_overridden && $salaryProcessing->leave_override_reason)
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Leave Override Reason</label>
                        <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; color: #333;">
                            {{ $salaryProcessing->leave_override_reason }}
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Advance Deduction Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Advance Deduction</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="advance_allocation_mode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Allocation Mode</label>
                <select name="advance_allocation_mode" id="advance_allocation_mode" {{ !$canEdit ? 'disabled' : 'required' }}
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                    <option value="OLDEST_FIRST" {{ old('advance_allocation_mode', $salaryProcessing->advance_allocation_mode) === 'OLDEST_FIRST' ? 'selected' : '' }}>Oldest First (Auto Allocate)</option>
                    <option value="SELECT_REFERENCE" {{ old('advance_allocation_mode', $salaryProcessing->advance_allocation_mode) === 'SELECT_REFERENCE' ? 'selected' : '' }}>Select Reference</option>
                </select>
                @if(!$canEdit)
                    <input type="hidden" name="advance_allocation_mode" value="{{ $salaryProcessing->advance_allocation_mode }}">
                @endif
                @error('advance_allocation_mode')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div id="advance_reference_field" style="display: {{ old('advance_allocation_mode', $salaryProcessing->advance_allocation_mode) === 'SELECT_REFERENCE' ? 'block' : 'none' }}; margin-bottom: 20px;">
                <label for="salary_advance_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Reference</label>
                <select name="salary_advance_id" id="salary_advance_id"
                        {{ !$canEdit ? 'disabled' : '' }}
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                    <option value="">-- Select Advance Reference --</option>
                    @foreach($pendingAdvances as $advance)
                        <option value="{{ $advance->id }}" 
                            {{ old('salary_advance_id', $salaryProcessing->salary_advance_id) == $advance->id ? 'selected' : '' }}
                            data-balance="{{ $advance->advance_balance_amount }}">
                            {{ $advance->advance_reference_no }} - Balance: ₹{{ number_format($advance->advance_balance_amount, 2) }}
                        </option>
                    @endforeach
                </select>
                @if(!$canEdit)
                    <input type="hidden" name="salary_advance_id" value="{{ $salaryProcessing->salary_advance_id }}">
                @endif
                @error('salary_advance_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="advance_deduction_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Deduction Amount</label>
                <input type="number" step="0.01" min="0" name="advance_deduction_amount" id="advance_deduction_amount" 
                    value="{{ old('advance_deduction_amount', $salaryProcessing->advance_deduction_amount) }}" 
                    {{ !$canEdit ? 'readonly' : 'required' }}
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                @error('advance_deduction_amount')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Net Payable Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Net Payable</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Net Payable Salary</label>
                <input type="text" id="net_payable_salary" readonly value="₹{{ number_format($salaryProcessing->net_payable_salary, 2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef; font-weight: 600; font-size: 16px;">
            </div>
        </div>

        <!-- Payment Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Payment Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="payment_status" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Status</label>
                    <select name="payment_status" id="payment_status" {{ !$canEdit ? 'disabled' : 'required' }}
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                        <option value="Pending" {{ old('payment_status', $salaryProcessing->payment_status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Paid" {{ old('payment_status', $salaryProcessing->payment_status) === 'Paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    @if(!$canEdit)
                        <input type="hidden" name="payment_status" value="{{ $salaryProcessing->payment_status }}">
                    @endif
                    @error('payment_status')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div id="paid_date_field" style="display: {{ old('payment_status', $salaryProcessing->payment_status) === 'Paid' ? 'block' : 'none' }};">
                    <label for="paid_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Paid Date</label>
                    <input type="date" name="paid_date" id="paid_date" 
                        value="{{ old('paid_date', $salaryProcessing->paid_date ? $salaryProcessing->paid_date->format('Y-m-d') : '') }}"
                        {{ !$canEdit ? 'readonly' : '' }}
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">
                    @error('paid_date')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="notes" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Enter any notes or remarks (optional)"
                          {{ !$canEdit ? 'readonly' : '' }}
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical; background: {{ !$canEdit ? '#e9ecef' : '#fff' }};">{{ old('notes', $salaryProcessing->notes) }}</textarea>
                @error('notes')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if($canEdit)
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <a href="{{ route('salary-masters.salary-processing.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Cancel
                </a>
                <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-save"></i> Update Salary Processing
                </button>
            </div>
        @else
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <a href="{{ route('salary-masters.salary-processing.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Back to List
                </a>
            </div>
        @endif
    </form>
</div>

@if($canEdit)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const advanceAllocationMode = document.getElementById('advance_allocation_mode');
    const advanceReferenceField = document.getElementById('advance_reference_field');
    const salaryAdvanceId = document.getElementById('salary_advance_id');
    const isLeaveOverridden = document.getElementById('is_leave_overridden');
    const leaveOverrideReasonField = document.getElementById('leave_override_reason_field');
    const leaveOverrideReason = document.getElementById('leave_override_reason');
    const paymentStatus = document.getElementById('payment_status');
    const paidDateField = document.getElementById('paid_date_field');
    const leaveDaysDeductible = document.getElementById('leave_days_deductible');
    
    advanceAllocationMode.addEventListener('change', function() {
        if (this.value === 'SELECT_REFERENCE') {
            advanceReferenceField.style.display = 'block';
            salaryAdvanceId.required = true;
        } else {
            advanceReferenceField.style.display = 'none';
            salaryAdvanceId.required = false;
        }
    });
    
    isLeaveOverridden.addEventListener('change', function() {
        if (this.checked) {
            leaveOverrideReasonField.style.display = 'block';
            leaveOverrideReason.required = true;
        } else {
            leaveOverrideReasonField.style.display = 'none';
            leaveOverrideReason.required = false;
            leaveOverrideReason.value = '';
        }
    });
    
    paymentStatus.addEventListener('change', function() {
        if (this.value === 'Paid') {
            paidDateField.style.display = 'block';
            document.getElementById('paid_date').required = true;
            if (!document.getElementById('paid_date').value) {
                document.getElementById('paid_date').value = new Date().toISOString().split('T')[0];
            }
        } else {
            paidDateField.style.display = 'none';
            document.getElementById('paid_date').required = false;
        }
    });
});
</script>
@endpush
@endif
@endsection

