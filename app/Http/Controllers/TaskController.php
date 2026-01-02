<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['creator'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('task_name', 'like', "%{$search}%")
                    ->orWhere('task_description', 'like', "%{$search}%")
                    ->orWhere('comments_updates', 'like', "%{$search}%");
            });
        }

        $tasks = $query->paginate(15)->withQueryString();

        return view('crm.tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('crm.tasks.create');
    }

    public function store(Request $request)
    {
        // Normalize empty time field before validation
        if ($request->has('notification_time') && empty(trim($request->notification_time))) {
            $request->merge(['notification_time' => null]);
        }

        $rules = [
            'task_name' => ['required', 'string', 'max:191'],
            'date' => ['nullable', 'date'],
            'task_description' => ['nullable', 'string'],
            'comments_updates' => ['nullable', 'string'],
            'notification_enabled' => ['nullable', 'boolean'],
        ];

        // Conditional validation for notification_time
        if ($request->has('notification_enabled') && $request->notification_enabled) {
            $rules['notification_time'] = ['required', 'date_format:H:i'];
        } else {
            // When notification is disabled, time is optional and can be null/empty
            $rules['notification_time'] = ['nullable'];
        }

        $data = $request->validate($rules);

        // Normalize notification_time: convert H:i:s to H:i if needed, or set to null if empty
        $notificationTime = null;
        if ($request->has('notification_enabled') && $request->notification_enabled) {
            if (!empty($data['notification_time'])) {
                // Ensure format is H:i (remove seconds if present)
                $time = $data['notification_time'];
                if (strlen($time) > 5) {
                    $time = substr($time, 0, 5);
                }
                $notificationTime = $time;
            }
        }

        $task = new Task();
        $task->task_name = $data['task_name'];
        $task->date = $data['date'] ?? date('Y-m-d');
        $task->task_description = $data['task_description'] ?? null;
        $task->comments_updates = $data['comments_updates'] ?? null;
        $task->notification_enabled = $request->has('notification_enabled');
        $task->notification_time = $notificationTime;

        $user = Auth::user();
        if ($user) {
            $task->organization_id = $user->organization_id ?? null;
            $task->branch_id = session('active_branch_id');
            $task->created_by = $user->id;
        }

        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load(['assignedEmployee', 'relatedCustomer', 'creator']);
        return view('crm.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        return view('crm.tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        // Normalize empty time field before validation
        if ($request->has('notification_time') && empty(trim($request->notification_time))) {
            $request->merge(['notification_time' => null]);
        }

        $rules = [
            'task_name' => ['required', 'string', 'max:191'],
            'date' => ['nullable', 'date'],
            'task_description' => ['nullable', 'string'],
            'comments_updates' => ['nullable', 'string'],
            'notification_enabled' => ['nullable', 'boolean'],
        ];

        // Conditional validation for notification_time
        if ($request->has('notification_enabled') && $request->notification_enabled) {
            $rules['notification_time'] = ['required', 'date_format:H:i'];
        } else {
            // When notification is disabled, time is optional and can be null/empty
            $rules['notification_time'] = ['nullable'];
        }

        $data = $request->validate($rules);

        // Normalize notification_time: convert H:i:s to H:i if needed, or set to null if empty
        $notificationTime = null;
        if ($request->has('notification_enabled') && $request->notification_enabled) {
            if (!empty($data['notification_time'])) {
                // Ensure format is H:i (remove seconds if present)
                $time = $data['notification_time'];
                if (strlen($time) > 5) {
                    $time = substr($time, 0, 5);
                }
                $notificationTime = $time;
            }
        }

        $task->task_name = $data['task_name'];
        $task->date = $data['date'] ?? $task->date ?? date('Y-m-d');
        $task->task_description = $data['task_description'] ?? null;
        $task->comments_updates = $data['comments_updates'] ?? null;
        $task->notification_enabled = $request->has('notification_enabled');
        $task->notification_time = $notificationTime;
        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Get pending task notifications for the current user
     */
    public function getPendingNotifications(Request $request)
    {
        $user = Auth::user();
        $now = \Carbon\Carbon::now();
        
        // Get tasks with notifications enabled that are due now
        $tasks = Task::where('notification_enabled', true)
            ->whereNotNull('notification_time')
            ->whereNotNull('date')
            ->when($user->organization_id, function($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->when(session('active_branch_id'), function($q) {
                $q->where('branch_id', session('active_branch_id'));
            })
            ->get()
            ->filter(function($task) use ($now) {
                if (!$task->date) {
                    return false;
                }
                
                // Combine task date and notification time
                try {
                    $taskDate = $task->date instanceof \Carbon\Carbon 
                        ? $task->date->format('Y-m-d') 
                        : \Carbon\Carbon::parse($task->date)->format('Y-m-d');
                    
                    $notificationDateTime = \Carbon\Carbon::parse($taskDate . ' ' . $task->notification_time);
                    
                    // Check if notification time is within current minute or just passed (within last 5 minutes)
                    // Also check if it's today's date
                    $diffInMinutes = abs($now->diffInMinutes($notificationDateTime));
                    $isToday = $notificationDateTime->format('Y-m-d') === $now->format('Y-m-d');
                    
                    // Show notification if it's today, within the current minute or just passed (within last 5 minutes)
                    return $isToday && $notificationDateTime->lte($now) && $diffInMinutes <= 5;
                } catch (\Exception $e) {
                    return false;
                }
            })
            ->map(function($task) {
                try {
                    $taskDate = $task->date instanceof \Carbon\Carbon 
                        ? $task->date->format('Y-m-d') 
                        : \Carbon\Carbon::parse($task->date)->format('Y-m-d');
                    
                    $notificationDateTime = \Carbon\Carbon::parse($taskDate . ' ' . $task->notification_time);
                    
                    return [
                        'id' => $task->id,
                        'task_name' => $task->task_name,
                        'task_description' => $task->task_description,
                        'notification_time' => $task->notification_time,
                        'notification_datetime' => $notificationDateTime->format('d-m-Y H:i'),
                    ];
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter()
            ->values();

        return response()->json($tasks);
    }
}

