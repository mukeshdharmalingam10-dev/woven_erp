@extends('layouts.dashboard')

@section('title', 'Create Salary Processing - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Create Salary Processing</h2>
        <a href="{{ route('salary-masters.salary-processing.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

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

    <form action="{{ route('salary-masters.salary-processing.store') }}" method="POST" id="salaryProcessingForm">
        @csrf

        <!-- Basic Information Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="salary_month" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Salary Month <span style="color: red;">*</span></label>
                    <input type="month" name="salary_month" id="salary_month" value="{{ old('salary_month') }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('salary_month')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="employee_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee <span style="color: red;">*</span></label>
                    <select name="employee_id" id="employee_id" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_name }} ({{ $employee->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="monthly_salary_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Monthly Salary Amount</label>
                <input type="text" id="monthly_salary_amount" readonly
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;"
                    placeholder="Will be auto-populated from salary setup">
            </div>
        </div>

        <!-- Attendance Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Attendance Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Total Working Days</label>
                    <input type="text" id="total_working_days" readonly
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Present Days</label>
                    <input type="text" id="present_days" readonly
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>

                <div>
                    <label for="leave_days_deductible" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Leave Days Deductible</label>
                    <input type="number" step="0.01" min="0" name="leave_days_deductible" id="leave_days_deductible" value="{{ old('leave_days_deductible', '0') }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Auto from attendance">
                    @error('leave_days_deductible')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Per Day Salary</label>
                    <input type="text" id="per_day_salary" readonly
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Leave Deduction Amount</label>
                    <input type="text" id="leave_deduction_amount" readonly
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; color: #333; font-weight: 500;">
                    <input type="checkbox" name="is_leave_overridden" id="is_leave_overridden" value="1" {{ old('is_leave_overridden') ? 'checked' : '' }}>
                    Override Leave Days
                </label>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Check this to manually override leave days</small>
            </div>

            <div id="leave_override_reason_field" style="display: none; margin-bottom: 20px;">
                <label for="leave_override_reason" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Leave Override Reason <span style="color: red;">*</span></label>
                <textarea name="leave_override_reason" id="leave_override_reason" rows="3"
                          placeholder="Enter reason for overriding leave days"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('leave_override_reason') }}</textarea>
                @error('leave_override_reason')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Advance Deduction Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Advance Deduction</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="advance_allocation_mode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Allocation Mode <span style="color: red;">*</span></label>
                <select name="advance_allocation_mode" id="advance_allocation_mode" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="OLDEST_FIRST" {{ old('advance_allocation_mode', 'OLDEST_FIRST') === 'OLDEST_FIRST' ? 'selected' : '' }}>Oldest First (Auto Allocate)</option>
                    <option value="SELECT_REFERENCE" {{ old('advance_allocation_mode') === 'SELECT_REFERENCE' ? 'selected' : '' }}>Select Reference</option>
                </select>
                @error('advance_allocation_mode')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div id="advance_reference_field" style="display: none; margin-bottom: 20px;">
                <label for="salary_advance_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Reference</label>
                <select name="salary_advance_id" id="salary_advance_id"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">-- Select Advance Reference --</option>
                </select>
                @error('salary_advance_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="advance_deduction_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Deduction Amount <span style="color: red;">*</span></label>
                <input type="number" step="0.01" min="0" name="advance_deduction_amount" id="advance_deduction_amount" value="{{ old('advance_deduction_amount', '0') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter advance deduction amount">
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Available balance will be shown below</small>
                @error('advance_deduction_amount')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div id="advance_balance_info" style="display: none; padding: 10px; background: #e7f3ff; border-radius: 5px; margin-bottom: 15px;">
                <small style="color: #004085; font-weight: 500;">Available Balance: <span id="available_balance">₹0.00</span></small>
            </div>
        </div>

        <!-- Net Payable Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Net Payable</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Net Payable Salary</label>
                <input type="text" id="net_payable_salary" readonly
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef; font-weight: 600; font-size: 16px;">
            </div>

            <div id="warning_banner" style="display: none; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px; margin-top: 15px;">
                <strong style="color: #856404;">Warning:</strong> <span id="warning_message" style="color: #856404;">Net payable salary is 0 or negative.</span>
            </div>
        </div>

        <!-- Payment Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Payment Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="payment_status" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Status <span style="color: red;">*</span></label>
                    <select name="payment_status" id="payment_status" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="Pending" {{ old('payment_status', 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Paid" {{ old('payment_status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    @error('payment_status')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div id="paid_date_field" style="display: none;">
                    <label for="paid_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Paid Date <span style="color: red;">*</span></label>
                    <input type="date" name="paid_date" id="paid_date" value="{{ old('paid_date') }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('paid_date')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="notes" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Enter any notes or remarks (optional)"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('notes') }}</textarea>
                @error('notes')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('salary-masters.salary-processing.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Create Salary Processing
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const employeeId = document.getElementById('employee_id');
    const salaryMonth = document.getElementById('salary_month');
    const advanceAllocationMode = document.getElementById('advance_allocation_mode');
    const advanceReferenceField = document.getElementById('advance_reference_field');
    const salaryAdvanceId = document.getElementById('salary_advance_id');
    const isLeaveOverridden = document.getElementById('is_leave_overridden');
    const leaveOverrideReasonField = document.getElementById('leave_override_reason_field');
    const paymentStatus = document.getElementById('payment_status');
    const paidDateField = document.getElementById('paid_date_field');
    
    // Function to fetch salary setup and attendance data
    function fetchSalaryData() {
        const empId = employeeId.value;
        const month = salaryMonth.value;
        
        if (!empId || !month) {
            clearSalaryData();
            return;
        }
        
        fetch(`{{ route('salary-masters.get-employee-salary-setup') }}?employee_id=${empId}&salary_month=${month}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    clearSalaryData();
                    return;
                }
                
                document.getElementById('monthly_salary_amount').value = data.monthly_salary_amount || '0.00';
                document.getElementById('total_working_days').value = data.total_working_days || '0';
                document.getElementById('present_days').value = data.present_days || '0';
                document.getElementById('leave_days_deductible').value = data.leave_days || '0';
                document.getElementById('per_day_salary').value = data.per_day_salary || '0.00';
                document.getElementById('leave_deduction_amount').value = data.leave_deduction_amount || '0.00';
                
                // Update advance dropdown
                salaryAdvanceId.innerHTML = '<option value="">-- Select Advance Reference --</option>';
                if (data.pending_advances && data.pending_advances.length > 0) {
                    data.pending_advances.forEach(advance => {
                        const option = document.createElement('option');
                        option.value = advance.id;
                        option.textContent = `${advance.advance_reference_no} - Balance: ${advance.advance_balance_amount}`;
                        option.dataset.balance = advance.advance_balance_raw;
                        salaryAdvanceId.appendChild(option);
                    });
                }
                
                calculateNetPayable();
            })
            .catch(error => {
                console.error('Error:', error);
                clearSalaryData();
            });
    }
    
    function clearSalaryData() {
        document.getElementById('monthly_salary_amount').value = '';
        document.getElementById('total_working_days').value = '';
        document.getElementById('present_days').value = '';
        document.getElementById('leave_days_deductible').value = '0';
        document.getElementById('per_day_salary').value = '';
        document.getElementById('leave_deduction_amount').value = '';
        calculateNetPayable();
    }
    
    function calculateNetPayable() {
        const monthlySalary = parseFloat(document.getElementById('monthly_salary_amount').value.replace(/,/g, '')) || 0;
        const leaveDeduction = parseFloat(document.getElementById('leave_deduction_amount').value.replace(/,/g, '')) || 0;
        const advanceDeduction = parseFloat(document.getElementById('advance_deduction_amount').value) || 0;
        const perDaySalary = parseFloat(document.getElementById('per_day_salary').value.replace(/,/g, '')) || 0;
        const leaveDays = parseFloat(document.getElementById('leave_days_deductible').value) || 0;
        
        // Recalculate leave deduction if leave days changed
        const calculatedLeaveDeduction = leaveDays * perDaySalary;
        document.getElementById('leave_deduction_amount').value = calculatedLeaveDeduction.toFixed(2);
        
        const netPayable = Math.max(0, monthlySalary - calculatedLeaveDeduction - advanceDeduction);
        document.getElementById('net_payable_salary').value = '₹' + netPayable.toFixed(2);
        
        // Show warning if net payable is 0
        const warningBanner = document.getElementById('warning_banner');
        if (netPayable <= 0) {
            warningBanner.style.display = 'block';
            document.getElementById('warning_message').textContent = 'Net payable salary is 0. Please review deductions.';
        } else {
            warningBanner.style.display = 'none';
        }
    }
    
    // Event listeners
    employeeId.addEventListener('change', fetchSalaryData);
    salaryMonth.addEventListener('change', fetchSalaryData);
    
    document.getElementById('leave_days_deductible').addEventListener('input', function() {
        if (!isLeaveOverridden.checked) return;
        calculateNetPayable();
    });
    
    document.getElementById('advance_deduction_amount').addEventListener('input', calculateNetPayable);
    
    advanceAllocationMode.addEventListener('change', function() {
        if (this.value === 'SELECT_REFERENCE') {
            advanceReferenceField.style.display = 'block';
            salaryAdvanceId.required = true;
            fetchSalaryData();
        } else {
            advanceReferenceField.style.display = 'none';
            salaryAdvanceId.required = false;
            salaryAdvanceId.value = '';
        }
        updateAdvanceBalance();
    });
    
    salaryAdvanceId.addEventListener('change', updateAdvanceBalance);
    
    function updateAdvanceBalance() {
        const selectedOption = salaryAdvanceId.options[salaryAdvanceId.selectedIndex];
        const balanceInfo = document.getElementById('advance_balance_info');
        if (selectedOption && selectedOption.dataset.balance) {
            const balance = parseFloat(selectedOption.dataset.balance);
            document.getElementById('available_balance').textContent = '₹' + balance.toFixed(2);
            balanceInfo.style.display = 'block';
        } else {
            balanceInfo.style.display = 'none';
        }
    }
    
    isLeaveOverridden.addEventListener('change', function() {
        if (this.checked) {
            leaveOverrideReasonField.style.display = 'block';
            document.getElementById('leave_override_reason').required = true;
        } else {
            leaveOverrideReasonField.style.display = 'none';
            document.getElementById('leave_override_reason').required = false;
            document.getElementById('leave_override_reason').value = '';
            fetchSalaryData(); // Reset to attendance data
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
    
    // Initial fetch if values exist
    if (employeeId.value && salaryMonth.value) {
        fetchSalaryData();
    }
    
    // Trigger payment status change on load
    paymentStatus.dispatchEvent(new Event('change'));
});
</script>
@endpush
@endsection

