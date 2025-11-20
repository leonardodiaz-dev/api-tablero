<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function getColumnsByBoardId($id)
    {
        $columns = Column::where('board_id', $id)
            ->with('tasks.subtasks')
            ->get();

        return response()->json($columns);
    }
}
