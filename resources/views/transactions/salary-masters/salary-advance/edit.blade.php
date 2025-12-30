@extends('layouts.dashboard')

@section('title', 'Edit Salary Advance - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Salary Advance</h2>
        <a href="{{ route('salary-masters.salary-advance.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('salary-masters.salary-advance.update', $salaryAdvance->id) }}" method="POST" id="advanceForm">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Advance Management</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Reference No</label>
                <input type="text" value="{{ $salaryAdvance->advance_reference_no }}" readonly
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="employee_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee <span style="color: red;">*</span></label>
                <select name="employee_id" id="employee_id" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id', $salaryAdvance->employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_name }} ({{ $employee->code }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="advance_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Date <span style="color: red;">*</span></label>
                    <input type="date" name="advance_date" id="advance_date" value="{{ old('advance_date', $salaryAdvance->advance_date->format('Y-m-d')) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('advance_date')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="advance_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Amount <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="advance_amount" id="advance_amount" value="{{ old('advance_amount', $salaryAdvance->advance_amount) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('advance_amount')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="advance_deduction_mode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Advance Deduction Mode <span style="color: red;">*</span></label>
                <select name="advance_deduction_mode" id="advance_deduction_mode" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="Full Deduction" {{ old('advance_deduction_mode', $salaryAdvance->advance_deduction_mode) === 'Full Deduction' ? 'selected' : '' }}>Full Deduction</option>
                    <option value="Monthly Installment" {{ old('advance_deduction_mode', $salaryAdvance->advance_deduction_mode) === 'Monthly Installment' ? 'selected' : '' }}>Monthly Installment</option>
                    <option value="Variable Installment" {{ old('advance_deduction_mode', $salaryAdvance->advance_deduction_mode) === 'Variable Installment' ? 'selected' : '' }}>Variable Installment</option>
                </select>
                @error('advance_deduction_mode')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div id="installment_fields">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="installment_start_month" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Installment Start Month</label>
                        <input type="month" name="installment_start_month" id="installment_start_month" value="{{ old('installment_start_month', $salaryAdvance->installment_start_month ? $salaryAdvance->installment_start_month->format('Y-m') : '') }}"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        @error('installment_start_month')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="installment_amount_field">
                        <label for="installment_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Installment Amount</label>
                        <input type="number" step="0.01" min="0.01" name="installment_amount" id="installment_amount" value="{{ old('installment_amount', $salaryAdvance->installment_amount) }}"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        @error('installment_amount')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="remarks" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('remarks', $salaryAdvance->remarks) }}</textarea>
                @error('remarks')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('salary-masters.salary-advance.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Update Salary Advance
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('advance_deduction_mode').addEventListener('change', function() {
        const mode = this.value;
        const installmentFields = document.getElementById('installment_fields');
        const installmentAmountField = document.getElementById('installment_amount_field');
        const installmentStartMonth = document.getElementById('installment_start_month');
        const installmentAmount = document.getElementById('installment_amount');
        
        if (mode === 'Monthly Installment' || mode === 'Variable Installment') {
            installmentFields.style.display = 'block';
            installmentStartMonth.required = true;
            
            if (mode === 'Monthly Installment') {
                installmentAmountField.style.display = 'block';
                installmentAmount.required = true;
            } else {
                installmentAmountField.style.display = 'none';
                installmentAmount.required = false;
            }
        } else {
            installmentFields.style.display = 'none';
            installmentStartMonth.required = false;
            installmentAmount.required = false;
        }
    });
    
    // Initialize on page load
    document.getElementById('advance_deduction_mode').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
