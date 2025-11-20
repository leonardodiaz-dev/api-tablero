<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Column;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function getAllTaskByBoard($id)
    {
        $tasks = Task::whereHas('column', function ($query) use ($id) {
            $query->where('board_id', $id);
        })
            ->with('subtasks')
            ->orderBy('order', 'asc')
            ->get();
        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request)
    {

        $data = $request->validated();

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'column_id' => $data['column_id']
        ]);

        if (isset($data['subtasks'])) {
            $task->subtasks()->createMany($data['subtasks']);
        }
        return response()->json($task->load('subtasks'));
    }
    public function update(UpdateTaskRequest $request, Task $task)
    {

        $data = $request->validated();

        $task->load('column');

        $newColumn = Column::where('board_id', $task->column->board_id)
            ->where('name', $data['status'])
            ->first();

        $newColumnId = $newColumn ? $newColumn->id : $task->column_id;

        $task->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'column_id'   => $newColumnId
        ]);

        $incomingIds = collect($data['subtasks'])->pluck('id')->filter();

        $task->subtasks()->whereNotIn('id', $incomingIds)->delete();


        foreach ($data['subtasks'] as $item) {

            if ($item['id'] === 0) {
                $task->subtasks()->create([
                    'title'       => $item['title'],
                    'isCompleted' => $item['isCompleted']
                ]);
            }

            $task->subtasks()
                ->where('id', $item['id'])
                ->update([
                    'title'       => $item['title'],
                    'isCompleted' => $item['isCompleted']
                ]);
        }

        return response()->json(
            $task->load('subtasks', 'column')
        );
    }

    public function changeCurrentStatus(Request $request, $id)
    {

        $request->validate([
            'column_id' => 'required|integer|exists:columns,id'
        ], [
            'column_id.required' => 'El campo column_id es obligatorio.',
            'column_id.exists' => 'La columna indicada no existe.'
        ]);

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $column = Column::find($request->column_id);

        $task->update([
            'column_id' => $column->id,
            'status' => $column->name
        ]);

        return response()->json($task->load('subtasks'));
    }

    public function changeOrderTasks(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|integer',
            'tasks.*.column_id' => 'required|integer',
            'tasks.*.order' => 'required|integer',
        ]);

        foreach ($request->tasks as $item) {

            $task = Task::find($item['id']);

            if ($task) {
                $task->update([
                    'column_id' => $item['column_id'],
                    'order'     => $item['order'],
                ]);
            }
        }

        return response()->json(['message' => 'Orden actualizado'], 200);
    }
    public function destroy(Task $task){

        $task->delete();

        return response()->json($task);
    }
}
