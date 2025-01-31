<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\TaskNotification;

class TaskController extends Controller
{
    public function index(){
        return Task::where('user_id', auth()->id())->get();
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'assignee_id' => 'nullable|exists:users,id',


        ]);

        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'assignee_id' => $validated['assignee_id'],
        ]);

        if ($task->assignee_id) {
            $assignee = User::find($task->assignee_id);
            $assignee->notify(new TaskNotification($task, 'You have been assigned a new task: ' . $task->title));
        }
        return response()->json($task);
    }

    public function update($id, Request $request)
    {
       

        // Mencari task berdasarkan id
        $task = Task::findOrFail($id);

        // Jika Bukan Usernya Maka Keluarkan Error Ini
        if ($task->assignee_id != $task->assignee_id) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Kamu Tidak Punya Hak Mengganti Task Ini',
            ], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'    =>'required',
            'due_date'  => 'required|date',
            'assignee_id' => 'required|exists:users,id', // Misalnya 'users' adalah tabel untuk assignee
        ]);

        // Update task dengan data yang dikirimkan
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->status = $validated['status'];
        $task->due_date= $validated['due_date'];
        $task->assignee_id = $validated['assignee_id']; // Mengubah assignee_id

        // Simpan perubahan
        $task->save();
        $originalData = $task->getOriginal();

        foreach ($validated as $key => $value) {
            if ($originalData[$key] != $value) {
                ActivityLog::create([
                    'task_id' => $task->id,
                    'user_id' => auth()->id(),
                    'activity' => "Updated $key from '{$originalData[$key]}' to '$value'",
                ]);
            }
        }
        // Response berhasil
        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ], 200);
    }

    public function destroy(Task $task)
    {
        // Cek ID user yang terautentikasi
        $authenticatedUserId = auth()->id();
        \Log::info('Authenticated User ID: ' . $authenticatedUserId);

        // Cek ID user yang terkait dengan task
        \Log::info('Task User ID: ' . $task->user_id);

        if ($task->user_id != $authenticatedUserId) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Kamu Tidak Punya Hak Menghapus Task Ini'
            ], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}
