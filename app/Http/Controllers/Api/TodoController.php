<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TodoExport;



class TodoController extends Controller
{
     public function index()
    {
        return response()->json(Todo::all());
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string',
            'assignee' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'nullable|numeric',
            'status' => 'nullable|in:pending,open,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        // Set nilai default jika belum diisi
        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['time_tracked'] = $validated['time_tracked'] ?? 0;

        $todo = Todo::create($validated);

        return response()->json($todo, 201);
    }

    public function show($id)
    {
        // Menampilkan Todo berdasarkan ID
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        return response()->json($todo);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'nullable|string',
            'assignee' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'time_tracked' => 'nullable|numeric',
            'status' => 'nullable|in:pending,open,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $todo->update($validated);

        return response()->json($todo);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['error' => 'Todo not found'], 404);
        }

        $todo->delete();

        return response()->json(['message' => 'Todo deleted successfully']);
    }

    // Fungsi untuk mengekspor Todo ke Excel
    public function export(Request $request)
    {
        $query = Todo::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('assignee')) {
            $query->whereIn('assignee', explode(',', $request->assignee));
        }

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('due_date', [$request->start, $request->end]);
        }

        if ($request->filled('min') && $request->filled('max')) {
            $query->whereBetween('time_tracked', [$request->min, $request->max]);
        }

        if ($request->filled('status')) {
            $query->whereIn('status', explode(',', $request->status));
        }

        if ($request->filled('priority')) {
            $query->whereIn('priority', explode(',', $request->priority));
        }

        $filtered = $query->get();

        return Excel::download(new TodoExport($filtered), 'todos.xlsx');
    }

    // Fungsi untuk menyediakan data chart dalam format JSON
    public function chart(Request $request)
    {
        $type = $request->query('type');

        switch ($type) {
            case 'status':
                return $this->getStatusSummary();
            case 'priority':
                return $this->getPrioritySummary();
            case 'assignee':
                return $this->getAssigneeSummary();
            default:
                return response()->json([
                    'error' => 'Invalid chart type. Available types: status, priority, assignee'
                ], 400);
        }
    }

    // Status Summary Chart Data
    private function getStatusSummary()
    {
        $statusCounts = Todo::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusSummary = [
            'pending' => $statusCounts->get('pending', 0),
            'open' => $statusCounts->get('open', 0),
            'in_progress' => $statusCounts->get('in_progress', 0),
            'completed' => $statusCounts->get('completed', 0)
        ];

        return response()->json([
            'status_summary' => $statusSummary
        ]);
    }

    // Priority Summary Chart Data
    private function getPrioritySummary()
    {
        $priorityCounts = Todo::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        $prioritySummary = [
            'low' => $priorityCounts->get('low', 0),
            'medium' => $priorityCounts->get('medium', 0),
            'high' => $priorityCounts->get('high', 0)
        ];

        return response()->json([
            'priority_summary' => $prioritySummary
        ]);
    }

    // Assignee Summary Chart Data
    private function getAssigneeSummary()
    {
        $assignees = Todo::select('assignee')
            ->distinct()
            ->whereNotNull('assignee')
            ->pluck('assignee');

        $assigneeSummary = [];

        foreach ($assignees as $assignee) {
            $totalTodos = Todo::where('assignee', $assignee)->count();
            $totalPendingTodos = Todo::where('assignee', $assignee)
                ->where('status', 'pending')
                ->count();
            $totalTimeTrackedCompletedTodos = Todo::where('assignee', $assignee)
                ->where('status', 'completed')
                ->sum('time_tracked');

            $assigneeSummary[$assignee] = [
                'total_todos' => $totalTodos,
                'total_pending_todos' => $totalPendingTodos,
                'total_timetracked_completed_todos' => $totalTimeTrackedCompletedTodos
            ];
        }

        return response()->json([
            'assignee_summary' => $assigneeSummary
        ]);
    }
}
