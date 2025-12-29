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
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by task name, description, or customer..."
                style="flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <select name="priority" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">All Priority</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
            </select>
            <select name="assigned_to" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->employee_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search') || request('status') || request('priority') || request('assigned_to'))
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
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Assigned To</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Due Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Priority</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Customer</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $task->task_name }}</td>
                            <td style="padding: 12px; color: #333;">{{ $task->assignedEmployee ? $task->assignedEmployee->employee_name : '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ optional($task->due_date)->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333;">
                                @if($task->priority === 'high')
                                    <span style="padding: 4px 8px; background: #dc3545; color: white; border-radius: 4px; font-size: 12px;">High</span>
                                @elseif($task->priority === 'medium')
                                    <span style="padding: 4px 8px; background: #ffc107; color: #333; border-radius: 4px; font-size: 12px;">Medium</span>
                                @else
                                    <span style="padding: 4px 8px; background: #28a745; color: white; border-radius: 4px; font-size: 12px;">Low</span>
                                @endif
                            </td>
                            <td style="padding: 12px; color: #333;">
                                @if($task->status === 'pending')
                                    <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">Pending</span>
                                @elseif($task->status === 'in_progress')
                                    <span style="padding: 4px 8px; background: #17a2b8; color: white; border-radius: 4px; font-size: 12px;">In Progress</span>
                                @else
                                    <span style="padding: 4px 8px; background: #28a745; color: white; border-radius: 4px; font-size: 12px;">Completed</span>
                                @endif
                            </td>
                            <td style="padding: 12px; color: #333;">{{ $task->relatedCustomer ? $task->relatedCustomer->customer_name : '-' }}</td>
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

