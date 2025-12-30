@extends('layouts.dashboard')

@section('title', 'Edit Salary Setup - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Salary Setup</h2>
        <a href="{{ route('salary-masters.salary-setup.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('salary-masters.salary-setup.update', $salarySetup->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Employee Salary Setup</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="employee_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee <span style="color: red;">*</span></label>
                <select name="employee_id" id="employee_id" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id', $salarySetup->employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_name }} ({{ $employee->code }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="monthly_salary_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Monthly Salary Amount <span style="color: red;">*</span></label>
                <input type="number" step="0.01" min="0" name="monthly_salary_amount" id="monthly_salary_amount" value="{{ old('monthly_salary_amount', $salarySetup->monthly_salary_amount) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter monthly salary amount">
                @error('monthly_salary_amount')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="salary_effective_from" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Salary Effective From <span style="color: red;">*</span></label>
                    <input type="month" name="salary_effective_from" id="salary_effective_from" value="{{ old('salary_effective_from', $salarySetup->salary_effective_from->format('Y-m')) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('salary_effective_from')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="salary_effective_to" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Salary Effective To</label>
                    <input type="month" name="salary_effective_to" id="salary_effective_to" value="{{ old('salary_effective_to', $salarySetup->salary_effective_to ? $salarySetup->salary_effective_to->format('Y-m') : '') }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Optional - leave blank if ongoing</small>
                    @error('salary_effective_to')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="status" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Status <span style="color: red;">*</span></label>
                <select name="status" id="status" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="Active" {{ old('status', $salarySetup->status) === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $salarySetup->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('salary-masters.salary-setup.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Update Salary Setup
            </button>
        </div>
    </form>
</div>
@endsection
