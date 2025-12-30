@extends('layouts.dashboard')

@section('title', 'Salary Advance - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('salary-masters.salary-advance.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Salary Advance</h2>
        @if($canWrite)
            <a href="{{ route('salary-masters.salary-advance.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Add New Advance
            </a>
        @endif
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('salary-masters.salary-advance.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by reference number, employee name or code..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if($search)
                <a href="{{ route('salary-masters.salary-advance.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($salaryAdvances->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Reference No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Advance Date</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Advance Amount</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Total Deducted</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Balance</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Deduction Mode</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaryAdvances as $index => $advance)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ $salaryAdvances->firstItem() + $index }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $advance->advance_reference_no }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $advance->employee->employee_name ?? 'N/A' }} ({{ $advance->employee->code ?? 'N/A' }})</td>
                            <td style="padding: 12px; color: #666;">{{ $advance->advance_date->format('d M Y') }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($advance->advance_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($advance->total_deducted ?? 0, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: {{ ($advance->advance_balance_amount ?? 0) > 0 ? '#dc3545' : '#28a745' }}; font-weight: 600;">₹{{ number_format($advance->advance_balance_amount ?? 0, 2) }}</td>
                            <td style="padding: 12px; text-align: center; color: #666;">{{ $advance->advance_deduction_mode }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('salary-masters.salary-advance.show', $advance->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('salary-masters.salary-advance.edit', $advance->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('salary-masters.salary-advance.destroy', $advance->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this salary advance?');">
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

        @include('partials.pagination', ['paginator' => $salaryAdvances, 'routeUrl' => route('salary-masters.salary-advance.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-money-bill-wave" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No salary advances found</p>
            @if($canWrite)
                <a href="{{ route('salary-masters.salary-advance.create') }}" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                    Create First Salary Advance
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
