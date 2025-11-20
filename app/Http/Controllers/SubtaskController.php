<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSubtaskRequest;
use App\Models\Subtask;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function update(UpdateSubtaskRequest $request, Subtask $subtask)
    {
        $subtask->update([
            'isCompleted' => $request->isCompleted
        ]);

        return response()->json(
            $subtask->load('task')
        );
    }
}
