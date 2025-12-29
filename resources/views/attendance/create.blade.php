@extends('layouts.dashboard')

@section('title', 'Mark Attendance - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Mark Attendance</h2>
        <a href="{{ route('attendances.index', ['date' => $selectedDate]) }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>There were some problems with your input:</strong>
            <ul style="margin-top: 8px; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('attendances.store') }}" id="attendanceForm">
        @csrf
        
        {{-- Date Field --}}
        <div style="margin-bottom: 25px;">
            <label for="date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Date <span style="color:red">*</span></label>
            <input type="date" name="date" id="date" required value="{{ old('date', $selectedDate) }}"
                   max="{{ date('Y-m-d') }}"
                   style="width: 100%; max-width: 300px; padding: 10px; border-radius: 5px; border: 1px solid #ddd;"
                   onchange="updateStatistics()">
            @error('date')
                <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Statistics Display --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Available Employees</div>
                <div style="font-size: 32px; font-weight: 700;" id="availableCount">{{ $availableEmployees->count() }}</div>
            </div>
            <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Employees Present</div>
                <div style="font-size: 32px; font-weight: 700;" id="presentCount">{{ $availableEmployees->count() - count($absenteeIds) }}</div>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Employees Absent</div>
                <div style="font-size: 32px; font-weight: 700;" id="absentCount">{{ count($absenteeIds) }}</div>
            </div>
        </div>

        {{-- Absentees Selection --}}
        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #333;">Mark Absentees</label>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6; max-height: 400px; overflow-y: auto;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px;">
                    @foreach($availableEmployees as $employee)
                        <label style="display: flex; align-items: center; padding: 10px; background: white; border: 2px solid #dee2e6; border-radius: 5px; cursor: pointer; transition: all 0.3s;"
                               onmouseover="this.style.borderColor='#667eea'" onmouseout="this.style.borderColor='#dee2e6'">
                            <input type="checkbox" name="absentees[]" value="{{ $employee->id }}"
                                   {{ in_array($employee->id, old('absentees', $absenteeIds)) ? 'checked' : '' }}
                                   onchange="updateStatistics()"
                                   style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                            <div style="flex: 1;">
                                <div style="font-weight: 500; color: #333;">{{ $employee->employee_name }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $employee->code }} - {{ $employee->department ?? 'N/A' }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <small style="color: #666; font-size: 12px; display: block; margin-top: 8px;">
                <i class="fas fa-info-circle"></i> Select employees who are absent. All other employees will be marked as Present.
            </small>
            @error('absentees')
                <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <a href="{{ route('attendances.index', ['date' => $selectedDate]) }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                Cancel
            </a>
            <button type="submit" style="padding: 10px 22px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Submit Attendance
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function updateStatistics() {
        const checkboxes = document.querySelectorAll('input[name="absentees[]"]');
        const totalAvailable = checkboxes.length;
        let absentCount = 0;
        
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                absentCount++;
            }
        });
        
        const presentCount = totalAvailable - absentCount;
        
        document.getElementById('availableCount').textContent = totalAvailable;
        document.getElementById('presentCount').textContent = presentCount;
        document.getElementById('absentCount').textContent = absentCount;
    }

    // Initialize statistics on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateStatistics();
    });
</script>
@endpush
@endsection

