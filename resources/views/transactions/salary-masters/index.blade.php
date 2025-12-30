@extends('layouts.dashboard')

@section('title', 'Salary Master - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('salary-masters.salary-setup.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="margin-bottom: 30px;">
        <h2 style="color: #333; font-size: 28px; margin: 0 0 10px 0;">Salary Master</h2>
        <p style="color: #666; font-size: 14px; margin: 0;">Manage employee salary setups, advances, and monthly salary processing</p>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Global Search -->
    <form method="GET" action="{{ route('salary-masters.index') }}" style="margin-bottom: 30px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search across all sections..."
                style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                <i class="fas fa-search"></i> Search
            </button>
            @if($search)
                <a href="{{ route('salary-masters.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Section 1: Employee Salary Setup -->
    <div style="margin-bottom: 40px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: white; font-size: 20px; margin: 0 0 5px 0;">
                        <i class="fas fa-user-tie"></i> Employee Salary Setup
                    </h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 13px; margin: 0;">Configure employee salary details and effective date ranges</p>
                </div>
                @if($canWrite)
                    <a href="{{ route('salary-masters.salary-setup.create') }}" style="padding: 10px 20px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 5px; font-weight: 500; border: 1px solid rgba(255,255,255,0.3); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-plus"></i> Add New Setup
                    </a>
                @endif
            </div>
        </div>
        
        <div style="padding: 20px;">
            @if($salarySetups->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee</th>
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Salary Type</th>
                                <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Monthly Salary</th>
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Effective From</th>
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Effective To</th>
                                <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                                <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salarySetups as $index => $setup)
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; color: #666;">{{ $salarySetups->firstItem() + $index }}</td>
                                    <td style="padding: 12px; color: #333; font-weight: 500;">{{ $setup->employee->employee_name ?? 'N/A' }} ({{ $setup->employee->code ?? 'N/A' }})</td>
                                    <td style="padding: 12px; color: #666;">{{ $setup->salary_type }}</td>
                                    <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($setup->monthly_salary_amount, 2) }}</td>
                                    <td style="padding: 12px; color: #666;">{{ $setup->salary_effective_from->format('M Y') }}</td>
                                    <td style="padding: 12px; color: #666;">{{ $setup->salary_effective_to ? $setup->salary_effective_to->format('M Y') : 'N/A' }}</td>
                                    <td style="padding: 12px; text-align: center;">
                                        <span style="padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 500; background: {{ $setup->status === 'Active' ? '#d4edda' : '#f8d7da' }}; color: {{ $setup->status === 'Active' ? '#155724' : '#721c24' }};">
                                            {{ $setup->status }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            @if($canRead)
                                                <a href="{{ route('salary-masters.salary-setup.show', $setup->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            @if($canWrite)
                                                <a href="{{ route('salary-masters.salary-setup.edit', $setup->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($canDelete)
                                                <form action="{{ route('salary-masters.salary-setup.destroy', $setup->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this salary setup?');">
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
                @if($salarySetups->hasPages())
                    <div style="margin-top: 15px;">
                        {{ $salarySetups->links() }}
                    </div>
                @endif
                <div style="margin-top: 15px; text-align: right;">
                    <a href="{{ route('salary-masters.salary-setup.index') }}" style="color: #667eea; text-decoration: none; font-size: 14px; font-weight: 500;">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <i class="fas fa-user-tie" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p style="font-size: 16px; margin-bottom: 10px;">No salary setups found</p>
                    @if($canWrite)
                        <a href="{{ route('salary-masters.salary-setup.create') }}" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                            Create First Salary Setup
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Section 2: Advance Management -->
    <div style="margin-bottom: 40px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: white; font-size: 20px; margin: 0 0 5px 0;">
                        <i class="fas fa-money-bill-wave"></i> Advance Management
                    </h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 13px; margin: 0;">Manage salary advances and deductions</p>
                </div>
                @if($canWrite)
                    <a href="{{ route('salary-masters.salary-advance.create') }}" style="padding: 10px 20px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 5px; font-weight: 500; border: 1px solid rgba(255,255,255,0.3); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-plus"></i> Add New Advance
                    </a>
                @endif
            </div>
        </div>
        
        <div style="padding: 20px;">
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
                @if($salaryAdvances->hasPages())
                    <div style="margin-top: 15px;">
                        {{ $salaryAdvances->links() }}
                    </div>
                @endif
                <div style="margin-top: 15px; text-align: right;">
                    <a href="{{ route('salary-masters.salary-advance.index') }}" style="color: #f5576c; text-decoration: none; font-size: 14px; font-weight: 500;">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <i class="fas fa-money-bill-wave" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p style="font-size: 16px; margin-bottom: 10px;">No salary advances found</p>
                    @if($canWrite)
                        <a href="{{ route('salary-masters.salary-advance.create') }}" style="padding: 10px 20px; background: #f5576c; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                            Create First Salary Advance
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Section 3: Monthly Salary Processing -->
    <div style="margin-bottom: 40px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 20px; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: white; font-size: 20px; margin: 0 0 5px 0;">
                        <i class="fas fa-calculator"></i> Monthly Salary Processing
                    </h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 13px; margin: 0;">Process monthly salaries with attendance and deductions</p>
                </div>
                @if($canWrite)
                    <a href="{{ route('salary-masters.salary-processing.create') }}" style="padding: 10px 20px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 5px; font-weight: 500; border: 1px solid rgba(255,255,255,0.3); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-plus"></i> Process Salary
                    </a>
                @endif
            </div>
        </div>
        
        <div style="padding: 20px;">
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
                @if($salaryProcessings->hasPages())
                    <div style="margin-top: 15px;">
                        {{ $salaryProcessings->links() }}
                    </div>
                @endif
                <div style="margin-top: 15px; text-align: right;">
                    <a href="{{ route('salary-masters.salary-processing.index') }}" style="color: #4facfe; text-decoration: none; font-size: 14px; font-weight: 500;">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <i class="fas fa-calculator" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p style="font-size: 16px; margin-bottom: 10px;">No salary processing records found</p>
                    @if($canWrite)
                        <a href="{{ route('salary-masters.salary-processing.create') }}" style="padding: 10px 20px; background: #4facfe; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                            Create First Salary Processing
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
