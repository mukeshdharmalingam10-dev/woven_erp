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
            <div style="font-size: 13px; color: #6b7280;">Date</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->date ? $task->date->format('d-m-Y') : '-' }}</div>
        </div>
        @if($task->notification_enabled && $task->notification_time)
        <div>
            <div style="font-size: 13px; color: #6b7280;">Notification Time</div>
            <div style="font-weight: 600; color: #111827;">{{ $task->notification_time }}</div>
        </div>
        @endif
        <div>
            <div style="font-size: 13px; color: #6b7280;">Notification Enabled</div>
            <div style="font-weight: 600; color: #111827;">
                @if($task->notification_enabled)
                    <span style="padding: 4px 8px; background: #28a745; color: white; border-radius: 4px; font-size: 12px;">Enabled</span>
                @else
                    <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">Disabled</span>
                @endif
            </div>
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

</div>
@endsection

