@extends('layouts.dashboard')

@section('title', 'Create Salary Record - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Create Salary Record</h2>
        <a href="{{ route('salary-masters.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('salary-masters.store') }}" method="POST" id="salaryForm">
        @csrf

        <!-- Basic Details Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Details</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
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

                <div>
                    <label for="salary_month" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Salary Month <span style="color: red;">*</span></label>
                    <input type="month" name="salary_month" id="salary_month" required
                           value="{{ old('salary_month', now()->format('Y-m')) }}"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('salary_month')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="monthly_salary_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Monthly Salary Amount <span style="color: red;">*</span></label>
                <input type="number" name="monthly_salary_amount" id="monthly_salary_amount" required
                       step="0.01" min="0.01"
                       value="{{ old('monthly_salary_amount') }}"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('monthly_salary_amount')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Advance Section -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Advance Section</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Applicable <span style="color: red;">*</span></label>
                <div style="display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="advance_applicable" value="1" id="advance_yes" {{ old('advance_applicable') == '1' ? 'checked' : '' }}
                               style="margin-right: 8px; width: 18px; height: 18px;">
                        <span>Yes</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="advance_applicable" value="0" id="advance_no" {{ old('advance_applicable', '0') == '0' ? 'checked' : '' }}
                               style="margin-right: 8px; width: 18px; height: 18px;">
                        <span>No</span>
                    </label>
                </div>
                @error('advance_applicable')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div id="deduction_mode_section" style="display: none; margin-bottom: 20px;">
                <label for="deduction_mode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Deduction Mode <span style="color: red;">*</span></label>
                <select name="deduction_mode" id="deduction_mode"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">-- Select Deduction Mode --</option>
                    <option value="Full Deduction" {{ old('deduction_mode') == 'Full Deduction' ? 'selected' : '' }}>Full Deduction</option>
                    <option value="Monthly Installment" {{ old('deduction_mode') == 'Monthly Installment' ? 'selected' : '' }}>Monthly Installment</option>
                    <option value="Variable Installment" {{ old('deduction_mode') == 'Variable Installment' ? 'selected' : '' }}>Variable Installment</option>
                </select>
                @error('deduction_mode')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Full Deduction Fields -->
            <div id="full_deduction_fields" style="display: none; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="full_deduction_month" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Full Deduction Month <span style="color: red;">*</span></label>
                        <input type="month" name="full_deduction_month" id="full_deduction_month"
                               value="{{ old('full_deduction_month') }}"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        <div id="full_deduction_warning" style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 5px; display: none;">
                            <span style="color: #856404; font-size: 13px;">
                                <i class="fas fa-exclamation-triangle"></i> Not deductible in this month.
                            </span>
                        </div>
                        @error('full_deduction_month')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="full_deduction_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Full Deduction Amount <span style="color: red;">*</span></label>
                        <input type="number" name="full_deduction_amount" id="full_deduction_amount"
                               step="0.01" min="0.01"
                               value="{{ old('full_deduction_amount') }}"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        @error('full_deduction_amount')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Monthly Installment Fields -->
            <div id="monthly_installment_fields" style="display: none; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="installment_start_month" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Installment Start Month <span style="color: red;">*</span></label>
                        <input type="month" name="installment_start_month" id="installment_start_month"
                               value="{{ old('installment_start_month') }}"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        @error('installment_start_month')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="installment_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Installment Amount <span style="color: red;">*</span></label>
                        <input type="number" name="installment_amount" id="installment_amount"
                               step="0.01" min="0.01"
                               value="{{ old('installment_amount') }}"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        @error('installment_amount')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Variable Installment Fields -->
            <div id="variable_installment_fields" style="display: none; margin-bottom: 20px;">
                <label for="variable_deduction_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Deduction Amount for the Month <span style="color: red;">*</span></label>
                <input type="number" name="variable_deduction_amount" id="variable_deduction_amount"
                       step="0.01" min="0.01"
                       value="{{ old('variable_deduction_amount') }}"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('variable_deduction_amount')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Attendance Section (Read-only) -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Attendance Section</h3>
            
            <div id="attendance_loading" style="padding: 15px; background: #fff3cd; border-radius: 5px; display: none; margin-bottom: 15px;">
                <span style="color: #856404; font-size: 13px;">
                    <i class="fas fa-spinner fa-spin"></i> Loading attendance data...
                </span>
            </div>

            <div id="attendance_no_data" style="padding: 15px; background: #f8d7da; border-radius: 5px; display: none; margin-bottom: 15px;">
                <span style="color: #721c24; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> Attendance not available for selected month.
                </span>
            </div>

            <div id="attendance_data" style="display: none;">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #666; font-weight: 500;">Number of Days Present</label>
                        <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; font-weight: 500;" id="days_present_display">0</div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #666; font-weight: 500;">Per Day Salary</label>
                        <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; font-weight: 500;" id="per_day_salary_display">₹0.00</div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #666; font-weight: 500;">Salary Amount as per Attendance</label>
                        <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; font-weight: 500;" id="salary_as_per_attendance_display">₹0.00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Section (Read-only) -->
        <div style="background: #e7f3ff; padding: 20px; border-radius: 5px; margin-bottom: 20px; border: 2px solid #667eea;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Summary</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #666; font-weight: 500;">Deduction Amount of the Month</label>
                    <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; color: #dc3545; font-weight: 600;" id="deduction_amount_display">₹0.00</div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #666; font-weight: 500;">Total Salary Amount After Deduction</label>
                    <div style="padding: 12px; background: #fff; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; color: #28a745; font-weight: 600;" id="net_salary_display">₹0.00</div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
            <a href="{{ route('salary-masters.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Save Salary Record
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Toggle deduction mode section based on advance applicable
    document.querySelectorAll('input[name="advance_applicable"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const deductionSection = document.getElementById('deduction_mode_section');
            if (this.value === '1') {
                deductionSection.style.display = 'block';
                document.getElementById('deduction_mode').required = true;
            } else {
                deductionSection.style.display = 'none';
                document.getElementById('deduction_mode').required = false;
                document.getElementById('deduction_mode').value = '';
                hideAllDeductionFields();
                calculateSummary();
            }
        });
    });

    // Toggle deduction mode fields
    document.getElementById('deduction_mode').addEventListener('change', function() {
        hideAllDeductionFields();
        if (this.value === 'Full Deduction') {
            document.getElementById('full_deduction_fields').style.display = 'block';
            document.getElementById('full_deduction_month').required = true;
            document.getElementById('full_deduction_amount').required = true;
        } else if (this.value === 'Monthly Installment') {
            document.getElementById('monthly_installment_fields').style.display = 'block';
            document.getElementById('installment_start_month').required = true;
            document.getElementById('installment_amount').required = true;
        } else if (this.value === 'Variable Installment') {
            document.getElementById('variable_installment_fields').style.display = 'block';
            document.getElementById('variable_deduction_amount').required = true;
        }
        calculateSummary();
    });

    function hideAllDeductionFields() {
        document.getElementById('full_deduction_fields').style.display = 'none';
        document.getElementById('monthly_installment_fields').style.display = 'none';
        document.getElementById('variable_installment_fields').style.display = 'none';
        document.getElementById('full_deduction_warning').style.display = 'none';
        
        document.getElementById('full_deduction_month').required = false;
        document.getElementById('full_deduction_amount').required = false;
        document.getElementById('installment_start_month').required = false;
        document.getElementById('installment_amount').required = false;
        document.getElementById('variable_deduction_amount').required = false;
    }

    // Fetch attendance when employee or month changes
    function fetchAttendance() {
        const employeeId = document.getElementById('employee_id').value;
        const salaryMonth = document.getElementById('salary_month').value;
        
        if (!employeeId || !salaryMonth) {
            document.getElementById('attendance_data').style.display = 'none';
            document.getElementById('attendance_loading').style.display = 'none';
            document.getElementById('attendance_no_data').style.display = 'none';
            return;
        }

        document.getElementById('attendance_loading').style.display = 'block';
        document.getElementById('attendance_data').style.display = 'none';
        document.getElementById('attendance_no_data').style.display = 'none';

        fetch('{{ route('salary-masters.get-attendance') }}?employee_id=' + employeeId + '&salary_month=' + salaryMonth, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('attendance_loading').style.display = 'none';
            
            if (data.error) {
                document.getElementById('attendance_no_data').style.display = 'block';
                return;
            }

            document.getElementById('days_present_display').textContent = data.days_present + ' / ' + data.days_in_month;
            document.getElementById('attendance_data').style.display = 'block';
            
            if (!data.attendance_available) {
                document.getElementById('attendance_no_data').style.display = 'block';
            }

            calculateSummary();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('attendance_loading').style.display = 'none';
            document.getElementById('attendance_no_data').style.display = 'block';
        });
    }

    document.getElementById('employee_id').addEventListener('change', fetchAttendance);
    document.getElementById('salary_month').addEventListener('change', fetchAttendance);

    // Calculate summary
    function calculateSummary() {
        const monthlySalary = parseFloat(document.getElementById('monthly_salary_amount').value) || 0;
        const salaryMonth = document.getElementById('salary_month').value;
        
        if (!salaryMonth || monthlySalary <= 0) {
            document.getElementById('deduction_amount_display').textContent = '₹0.00';
            document.getElementById('net_salary_display').textContent = '₹0.00';
            return;
        }

        // Get days in month
        const monthDate = new Date(salaryMonth + '-01');
        const daysInMonth = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0).getDate();
        
        // Get days present from attendance
        const daysPresentText = document.getElementById('days_present_display').textContent;
        let daysPresent = 0;
        if (daysPresentText && daysPresentText.includes('/')) {
            daysPresent = parseInt(daysPresentText.split('/')[0].trim()) || 0;
        }

        const absentDays = daysInMonth - daysPresent;
        const perDaySalary = daysInMonth > 0 ? monthlySalary / daysInMonth : 0;
        const salaryAsPerAttendance = Math.max(0, monthlySalary - (absentDays * perDaySalary));

        // Update per day salary and salary as per attendance
        document.getElementById('per_day_salary_display').textContent = '₹' + perDaySalary.toFixed(2);
        document.getElementById('salary_as_per_attendance_display').textContent = '₹' + salaryAsPerAttendance.toFixed(2);

        // Calculate deduction
        let deductionAmount = 0;
        const advanceApplicable = document.querySelector('input[name="advance_applicable"]:checked')?.value === '1';
        
        if (advanceApplicable) {
            const deductionMode = document.getElementById('deduction_mode').value;
            
            if (deductionMode === 'Full Deduction') {
                const fullDeductionMonth = document.getElementById('full_deduction_month').value;
                if (fullDeductionMonth && fullDeductionMonth === salaryMonth) {
                    deductionAmount = parseFloat(document.getElementById('full_deduction_amount').value) || 0;
                    document.getElementById('full_deduction_warning').style.display = 'none';
                } else if (fullDeductionMonth) {
                    document.getElementById('full_deduction_warning').style.display = 'block';
                }
            } else if (deductionMode === 'Monthly Installment') {
                const installmentStartMonth = document.getElementById('installment_start_month').value;
                if (installmentStartMonth && installmentStartMonth <= salaryMonth) {
                    deductionAmount = parseFloat(document.getElementById('installment_amount').value) || 0;
                }
            } else if (deductionMode === 'Variable Installment') {
                deductionAmount = parseFloat(document.getElementById('variable_deduction_amount').value) || 0;
            }
        }

        // Cap deduction by salary as per attendance
        deductionAmount = Math.min(deductionAmount, salaryAsPerAttendance);

        // Calculate net salary
        const netSalary = Math.max(0, salaryAsPerAttendance - deductionAmount);

        document.getElementById('deduction_amount_display').textContent = '₹' + deductionAmount.toFixed(2);
        document.getElementById('net_salary_display').textContent = '₹' + netSalary.toFixed(2);
    }

    // Add event listeners for calculation
    document.getElementById('monthly_salary_amount').addEventListener('input', calculateSummary);
    document.getElementById('full_deduction_month').addEventListener('change', calculateSummary);
    document.getElementById('full_deduction_amount').addEventListener('input', calculateSummary);
    document.getElementById('installment_start_month').addEventListener('change', calculateSummary);
    document.getElementById('installment_amount').addEventListener('input', calculateSummary);
    document.getElementById('variable_deduction_amount').addEventListener('input', calculateSummary);

    // Initialize on page load
    if (document.getElementById('advance_yes').checked) {
        document.getElementById('deduction_mode_section').style.display = 'block';
    }
    
    const deductionMode = document.getElementById('deduction_mode').value;
    if (deductionMode) {
        document.getElementById('deduction_mode').dispatchEvent(new Event('change'));
    }

    // Fetch attendance if employee and month are already selected
    if (document.getElementById('employee_id').value && document.getElementById('salary_month').value) {
        fetchAttendance();
    }
</script>
@endpush
@endsection

