<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::where('user_id', Auth::id())
            ->orderBy('position', 'asc')
            ->get();

        $todoList = $todos->where('status', 'To Do');
        $progressList = $todos->where('status', 'Progress');
        $doneList = $todos->where('status', 'Done');

        return view('admin.todo.index', compact('todoList', 'progressList', 'doneList'));
    }

    public function store(TodoRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['position'] = Todo::where('user_id', Auth::id())->where('status', $data['status'])->count() + 1;

        $todo = Todo::create($data);

        AuditLogService::log('CREATE', 'Kanban To-Do', "Menambahkan tugas baru: {$todo->judul}");

        return response()->json([
            'success' => true,
            'message' => 'Tugas baru berhasil ditambahkan.',
            'data' => $todo,
        ]);
    }

    public function updateStatus(Request $request, Todo $todo)
    {
        $request->validate([
            'status' => 'required|in:To Do,Progress,Done',
            'position' => 'nullable|integer',
        ]);

        $todo->update([
            'status' => $request->status,
            'position' => $request->position ?? $todo->position,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status tugas diperbarui.',
        ]);
    }

    public function destroy(Todo $todo)
    {
        $judul = $todo->judul;
        $todo->delete();

        AuditLogService::log('DELETE', 'Kanban To-Do', "Menghapus tugas: {$judul}");

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus.',
        ]);
    }
}
