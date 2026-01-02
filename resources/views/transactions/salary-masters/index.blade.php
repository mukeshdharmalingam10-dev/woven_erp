@extends('layouts.dashboard')

@section('title', 'Salary Master - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('salary-masters.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Salary Master</h2>
        @if($canWrite)
            <a href="{{ route('salary-masters.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Create Salary Record
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('salary-masters.index') }}" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; align-items: end;">
            <div>
                <label for="search" style="display: block; margin-bottom: 5px; color: #333; font-size: 14px;">Search Employee</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by employee name or code..."
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div>
                <label for="employee_id" style="display: block; margin-bottom: 5px; color: #333; font-size: 14px;">Employee</label>
                <select name="employee_id" id="employee_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_name }} ({{ $employee->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="salary_month" style="display: block; margin-bottom: 5px; color: #333; font-size: 14px;">Salary Month</label>
                <input type="month" name="salary_month" value="{{ request('salary_month') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(request('search') || request('employee_id') || request('salary_month'))
                    <a href="{{ route('salary-masters.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center;">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if($salaryMasters->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Salary Month</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Monthly Salary</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Days Present</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Salary (Attendance)</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Deduction</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Net Salary</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaryMasters as $index => $salary)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #333;">{{ $salaryMasters->firstItem() + $index }}</td>
                            <td style="padding: 12px; color: #333;">{{ $salary->employee->employee_name ?? 'N/A' }}<br><small style="color: #666;">{{ $salary->employee->code ?? '' }}</small></td>
                            <td style="padding: 12px; color: #333;">{{ \Carbon\Carbon::parse($salary->salary_month)->format('M Y') }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($salary->monthly_salary_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: center; color: #333;">{{ $salary->days_present }}/{{ $salary->days_in_month }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($salary->salary_as_per_attendance, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #dc3545; font-weight: 500;">₹{{ number_format($salary->deduction_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #28a745; font-weight: 600;">₹{{ number_format($salary->net_salary, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('salary-masters.show', $salary->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('salary-masters.edit', $salary->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('salary-masters.destroy', $salary->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this salary record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $salaryMasters, 'routeUrl' => route('salary-masters.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-money-bill-wave" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No salary records found</p>
            @if($canWrite)
                <a href="{{ route('salary-masters.create') }}" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                    Create First Salary Record
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

