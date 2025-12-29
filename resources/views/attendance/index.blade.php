@extends('layouts.dashboard')

@section('title', 'Attendance - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('attendances.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Attendance</h2>
        @if($canWrite)
            <a href="{{ route('attendances.create', ['date' => $selectedDate]) }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Mark Attendance
            </a>
        @endif
    </div>

    {{-- Date Filter and Statistics --}}
    <form method="GET" action="{{ route('attendances.index') }}" style="margin-bottom: 25px;">
        <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px;">
            <div style="flex: 1; max-width: 250px;">
                <label for="date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Select Date</label>
                <input type="date" name="date" id="date" value="{{ $selectedDate }}"
                       max="{{ date('Y-m-d') }}"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;"
                       onchange="this.form.submit()">
            </div>
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 24px;">
                <i class="fas fa-search"></i> View
            </button>
        </div>
    </form>

    {{-- Statistics Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; color: white;">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Available Employees</div>
            <div style="font-size: 32px; font-weight: 700;">{{ $totalAvailable }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 20px; border-radius: 8px; color: white;">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Employees Present</div>
            <div style="font-size: 32px; font-weight: 700;">{{ $totalPresent }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; border-radius: 8px; color: white;">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Employees Absent</div>
            <div style="font-size: 32px; font-weight: 700;">{{ $totalAbsent }}</div>
        </div>
    </div>

    @if($attendances->count() > 0)
        {{-- Attendance Records Table --}}
        <div style="margin-bottom: 20px;">
            <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">Attendance Records for {{ date('d-m-Y', strtotime($selectedDate)) }}</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee Name</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Code</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Department</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availableEmployees as $index => $employee)
                            @php
                                $attendance = $attendances->get($employee->id);
                                $status = $attendance ? $attendance->status : 'Not Marked';
                            @endphp
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; color: #666;">{{ $index + 1 }}</td>
                                <td style="padding: 12px; color: #333; font-weight: 500;">{{ $employee->employee_name }}</td>
                                <td style="padding: 12px; color: #666;">{{ $employee->code }}</td>
                                <td style="padding: 12px; color: #333;">{{ $employee->department ?? '-' }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($status === 'Present')
                                        <span style="padding: 6px 12px; background: #28a745; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                            <i class="fas fa-check"></i> Present
                                        </span>
                                    @elseif($status === 'Absent')
                                        <span style="padding: 6px 12px; background: #dc3545; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                            <i class="fas fa-times"></i> Absent
                                        </span>
                                    @else
                                        <span style="padding: 6px 12px; background: #6c757d; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                            Not Marked
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($canWrite && $attendance)
                                        <a href="{{ route('attendances.edit', ['date' => $selectedDate]) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($canWrite)
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('attendances.edit', ['date' => $selectedDate]) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    <i class="fas fa-edit"></i> Edit Attendance
                </a>
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-calendar-check" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 20px;">No attendance recorded for this date.</p>
            @if($canWrite)
                <a href="{{ route('attendances.create', ['date' => $selectedDate]) }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Mark Attendance for This Date
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

