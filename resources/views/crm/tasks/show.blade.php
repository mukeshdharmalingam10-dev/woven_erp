@extends('layouts.dashboard')

@section('title', 'View Task - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Task Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('tasks.edit', $task->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('tasks.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Task Name</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->task_name }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Assigned To</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->assignedEmployee ? $task->assignedEmployee->employee_name : '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($task->due_date)->format('d-m-Y') }}</div>
        </div>
        @if($task->notification_enabled && $task->notification_time)
        <div>
            <div style="font-size: 13px; color: #6b7280;">Notification Time</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->notification_time }}</div>
        </div>
        @endif
        <div>
            <div style="font-size: 13px; color: #6b7280;">Priority</div>
            <div style="font-weight: 600; color: #111827;">
                @if($task->priority === 'high')
                    <span style="padding: 4px 8px; background: #dc3545; color: white; border-radius: 4px; font-size: 12px;">High</span>
                @elseif($task->priority === 'medium')
                    <span style="padding: 4px 8px; background: #ffc107; color: #333; border-radius: 4px; font-size: 12px;">Medium</span>
                @else
                    <span style="padding: 4px 8px; background: #28a745; color: white; border-radius: 4px; font-size: 12px;">Low</span>
                @endif
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Status</div>
            <div style="font-weight: 600; color: #111827;">
                @if($task->status === 'pending')
                    <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">Pending</span>
                @elseif($task->status === 'in_progress')
                    <span style="padding: 4px 8px; background: #17a2b8; color: white; border-radius: 4px; font-size: 12px;">In Progress</span>
                @else
                    <span style="padding: 4px 8px; background: #28a745; color: white; border-radius: 4px; font-size: 12px;">Completed</span>
                @endif
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Related Customer</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->relatedCustomer ? $task->relatedCustomer->customer_name : '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Task Type</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->task_type ?: '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">External Agency</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->external_agency ?: '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Created By</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->creator ? $task->creator->name : '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Created At</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->created_at->format('d-m-Y H:i') }}</div>
        </div>
    </div>

    @if($task->task_description)
        <div style="margin-bottom: 20px;">
            <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">Task Description</div>
            <div style="padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; white-space: pre-wrap; color: #111827;">
                {{ $task->task_description }}
            </div>
        </div>
    @endif

    @if($task->comments_updates)
        <div style="margin-bottom: 20px;">
            <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">Comments/Updates</div>
            <div style="padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; white-space: pre-wrap; color: #111827;">
                {{ $task->comments_updates }}
            </div>
        </div>
    @endif

    @if($task->is_recurring)
        <div style="margin-bottom: 20px; padding: 15px; background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px;">
            <h4 style="margin: 0 0 10px 0; color: #333;">Recurring Task Information</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <div style="font-size: 13px; color: #6b7280;">Repeat Interval</div>
                    <div style="font-weight: 600; color: #111827;">{{ ucfirst($task->repeat_interval) }}</div>
                </div>
                @if($task->recurring_end_date)
                    <div>
                        <div style="font-size: 13px; color: #6b7280;">End Date</div>
                        <div style="font-weight: 600; color: #111827;">{{ $task->recurring_end_date->format('d-m-Y') }}</div>
                    </div>
                @endif
                <div>
                    <div style="font-size: 13px; color: #6b7280;">Notifications</div>
                    <div style="font-weight: 600; color: #111827;">
                        {{ $task->notification_enabled ? 'Enabled' : 'Disabled' }}
                        @if($task->notification_enabled && $task->notification_time)
                            <span style="font-size: 12px; color: #6b7280;">({{ $task->notification_time }})</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

