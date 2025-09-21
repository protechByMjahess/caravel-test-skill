<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('view', $project);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $task = $project->tasks()->create([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'status' => 'todo',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task created successfully!',
                'task' => $task
            ]);
        }

        return redirect()->back()
            ->with('success', 'Task created successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $task);
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:todo,in_progress,done',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $task->update($request->only(['title', 'status', 'due_date']));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task updated successfully!',
                'task' => $task
            ]);
        }

        return redirect()->back()
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $task);
        
        $task->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Task deleted successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Task deleted successfully!');
    }
}
