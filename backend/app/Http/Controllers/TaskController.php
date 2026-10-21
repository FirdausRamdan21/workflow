<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of all tasks sorted by deadline.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::orderBy('deadline', 'asc')->get();
        return response()->json([
            'data' => $tasks,
            'count' => $tasks->count(),
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'prioritas' => 'required|in:rendah,sedang,tinggi',
            'status' => 'sometimes|in:belum_dikerjakan,sedang_dikerjakan,selesai',
        ]);

        $task = Task::create($validated);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'data' => $task,
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'sometimes|date',
            'prioritas' => 'sometimes|in:rendah,sedang,tinggi',
            'status' => 'sometimes|in:belum_dikerjakan,sedang_dikerjakan,selesai',
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task,
        ]);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Get summary statistics of all tasks.
     */
    public function summary(): JsonResponse
    {
        $total = Task::count();
        $selesai = Task::where('status', 'selesai')->count();
        $sedang_dikerjakan = Task::where('status', 'sedang_dikerjakan')->count();
        $belum_selesai = Task::whereIn('status', ['belum_dikerjakan', 'sedang_dikerjakan'])->count();

        return response()->json([
            'total' => $total,
            'selesai' => $selesai,
            'sedang_dikerjakan' => $sedang_dikerjakan,
            'belum_selesai' => $belum_selesai,
        ]);
    }
}
