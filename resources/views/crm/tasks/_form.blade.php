@php
    $editing = isset($task);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="task_name" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Task Name <span style="color:red">*</span></label>
        <input type="text" name="task_name" id="task_name" required
               value="{{ old('task_name', $editing ? $task->task_name : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('task_name')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="notification_time" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Time <span id="time_required" style="color:red; display: none;">*</span></label>
        <input type="time" name="notification_time" id="notification_time" step="60"
               value="{{ old('notification_time', $editing && $task->notification_time ? (strlen($task->notification_time) > 5 ? substr($task->notification_time, 0, 5) : $task->notification_time) : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('notification_time')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 28px;">
            <input type="checkbox" name="notification_enabled" id="notification_enabled" value="1"
                   {{ old('notification_enabled', $editing && $task->notification_enabled ? 'checked' : '') }}
                   onchange="toggleNotificationFields()">
            <span style="font-weight: 600; color: #333;">Enable Notification</span>
        </label>
        @error('notification_enabled')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

<div style="margin-bottom: 20px;">
    <label for="task_description" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Task Description</label>
    <textarea name="task_description" id="task_description" rows="4"
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('task_description', $editing ? $task->task_description : '') }}</textarea>
    @error('task_description')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

<div style="margin-bottom: 20px;">
    <label for="comments_updates" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Comments</label>
    <textarea name="comments_updates" id="comments_updates" rows="4"
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('comments_updates', $editing ? $task->comments_updates : '') }}</textarea>
    @error('comments_updates')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
    function toggleNotificationFields() {
        var isEnabled = document.getElementById('notification_enabled').checked;
        var timeField = document.getElementById('notification_time');
        var timeRequired = document.getElementById('time_required');
        
        if (isEnabled) {
            timeRequired.style.display = 'inline';
            timeField.setAttribute('required', 'required');
        } else {
            timeRequired.style.display = 'none';
            timeField.removeAttribute('required');
            timeField.value = '';
        }
    }

    // Ensure form only submits valid time format or empty
    document.addEventListener('DOMContentLoaded', function() {
        toggleNotificationFields();
        
        // Handle form submission to ensure proper time format
        var form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                var notificationEnabled = document.getElementById('notification_enabled').checked;
                var timeField = document.getElementById('notification_time');
                
                // If notification is disabled, clear the time field
                if (!notificationEnabled) {
                    timeField.value = '';
                } else {
                    // Ensure time is in H:i format (remove seconds if present)
                    if (timeField.value && timeField.value.length > 5) {
                        timeField.value = timeField.value.substring(0, 5);
                    }
                }
            });
        }
    });
</script>
@endpush
