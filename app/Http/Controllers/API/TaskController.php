<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', auth()->id())
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->when($request->plan_id, fn($q, $id) => $q->where('marketing_plan_id', $id))
            ->when($request->campaign_id, fn($q, $id) => $q->where('campaign_id', $id))
            ->with(['assignee:id,name', 'marketingPlan:id,name', 'campaign:id,name'])
            ->orderBy('order')
            ->orderBy('due_date')
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed,cancelled',
            'priority' => 'in:low,medium,high,urgent',
            'category' => 'nullable|string',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'marketing_plan_id' => 'nullable|exists:marketing_plans,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'assigned_to' => 'nullable|exists:users,id',
            'checklist' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $task = Task::create([
            ...$validated,
            'user_id' => auth()->id(),
            'order' => Task::where('user_id', auth()->id())->max('order') + 1,
        ]);

        return response()->json(['success' => true, 'data' => $task], 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load(['comments.user', 'reminders', 'assignee']);
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update($request->all());

        if ($request->status === 'completed' && !$task->completed_at) {
            $task->update(['completed_at' => now()]);
        }

        return response()->json(['success' => true, 'data' => $task]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $request->validate(['tasks' => 'required|array']);
        
        foreach ($request->tasks as $index => $taskId) {
            Task::where('id', $taskId)->where('user_id', auth()->id())
                ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function addComment(Request $request, Task $task)
    {
        $this->authorize('view', $task);
        
        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json(['success' => true, 'data' => $comment->load('user')]);
    }

    public function updateChecklist(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update(['checklist' => $request->checklist]);
        return response()->json(['success' => true, 'data' => $task]);
    }

    // Kanban board stats - optimized single query
    public function boardStats()
    {
        $userId = auth()->id();
        $today = today();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        $tasks = Task::where('user_id', $userId)
            ->selectRaw("
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status != 'completed' AND due_date < ? THEN 1 ELSE 0 END) as overdue,
                SUM(CASE WHEN DATE(due_date) = ? THEN 1 ELSE 0 END) as due_today,
                SUM(CASE WHEN due_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as due_this_week
            ", [$today, $today, $weekStart, $weekEnd])
            ->first();
        
        return response()->json([
            'pending' => (int) $tasks->pending,
            'in_progress' => (int) $tasks->in_progress,
            'completed' => (int) $tasks->completed,
            'overdue' => (int) $tasks->overdue,
            'due_today' => (int) $tasks->due_today,
            'due_this_week' => (int) $tasks->due_this_week,
        ]);
    }
}
