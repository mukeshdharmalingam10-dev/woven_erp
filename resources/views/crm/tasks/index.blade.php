@extends('layouts.dashboard')

@section('title', 'Tasks - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('tasks.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Tasks</h2>
        @if($canWrite)
            <a href="{{ route('tasks.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Task
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('tasks.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by task name, description, or comments..."
                style="flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('tasks.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($tasks->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Task Name</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Time</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Task Description</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Comments</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $task->task_name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $task->created_at->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #666;">
                                @if($task->notification_time)
                                    @php
                                        // Parse the time string (could be H:i:s or H:i format)
                                        $timeStr = $task->notification_time;
                                        // Extract hours and minutes (handle both H:i:s and H:i)
                                        $timeParts = explode(':', $timeStr);
                                        $hour = (int)$timeParts[0];
                                        $minute = isset($timeParts[1]) ? str_pad($timeParts[1], 2, '0', STR_PAD_LEFT) : '00';
                                        // Convert to 12-hour format
                                        $period = $hour >= 12 ? 'PM' : 'AM';
                                        $hour12 = $hour == 0 ? 12 : ($hour > 12 ? $hour - 12 : $hour);
                                        $formattedTime = sprintf('%d:%s %s', $hour12, $minute, $period);
                                    @endphp
                                    {{ $formattedTime }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 12px; color: #333;">
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $task->task_description }}">
                                    {{ $task->task_description ?: '-' }}
                                </div>
                            </td>
                            <td style="padding: 12px; color: #333;">
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $task->comments_updates }}">
                                    {{ $task->comments_updates ?: '-' }}
                                </div>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('tasks.show', $task->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('tasks.edit', $task->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                <i class="fas fa-trash"></i> Delete
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

        @include('partials.pagination', ['paginator' => $tasks, 'routeUrl' => route('tasks.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No tasks found.</p>
            @if($canWrite)
                <a href="{{ route('tasks.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Task
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

