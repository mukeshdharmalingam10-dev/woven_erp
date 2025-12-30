@extends('layouts.dashboard')

@section('title', 'Monthly Salary Processing - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('salary-masters.salary-setup.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Monthly Salary Processing</h2>
        @if($canWrite)
            <a href="{{ route('salary-masters.salary-processing.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Process Salary
            </a>
        @endif
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('salary-masters.salary-processing.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by employee name or code..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if($search)
                <a href="{{ route('salary-masters.salary-processing.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($salaryProcessings->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Salary Month</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Monthly Salary</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Leave Deduction</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Advance Deduction</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Net Payable</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Payment Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaryProcessings as $index => $processing)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ $salaryProcessings->firstItem() + $index }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $processing->employee->employee_name ?? 'N/A' }} ({{ $processing->employee->code ?? 'N/A' }})</td>
                            <td style="padding: 12px; color: #666;">{{ $processing->salary_month->format('M Y') }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($processing->monthly_salary_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #dc3545;">₹{{ number_format($processing->leave_deduction_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #dc3545;">₹{{ number_format($processing->advance_deduction_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 600;">₹{{ number_format($processing->net_payable_salary, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <span style="padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 500; background: {{ $processing->payment_status === 'Paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $processing->payment_status === 'Paid' ? '#155724' : '#856404' }};">
                                    {{ $processing->payment_status }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('salary-masters.salary-processing.show', $processing->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($canWrite && ($processing->payment_status === 'Pending' || $user->isAdmin()))
                                        <a href="{{ route('salary-masters.salary-processing.edit', $processing->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($canDelete && ($processing->payment_status === 'Pending' || $user->isAdmin()))
                                        <form action="{{ route('salary-masters.salary-processing.destroy', $processing->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this salary processing?');">
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

        @include('partials.pagination', ['paginator' => $salaryProcessings, 'routeUrl' => route('salary-masters.salary-processing.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-calculator" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No salary processing records found</p>
            @if($canWrite)
                <a href="{{ route('salary-masters.salary-processing.create') }}" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                    Create First Salary Processing
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

