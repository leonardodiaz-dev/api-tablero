<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(Request $request)
    {
        $boards = Board::with("columns")->get();

        return response()->json($boards);
    }

    public function getFirstIdBoard()
    {
        $board = Board::first();
        return response()->json($board->id);
    }

    public function store(StoreBoardRequest $request)
    {
        $data = $request->validated();

        $board = Board::create([
            'name' => $data['name']
        ]);

        if (!empty($data['columns'])) {
            $board->columns()->createMany($data['columns']);
        }

        return response()->json(
            $board->load('columns'),
            201
        );
    }

    public function update(UpdateBoardRequest $request, Board $board)
    {
        $data = $request->validated();

        $board->update([
            'name' => $data['name'],
        ]);
        
        $incoming = collect($data['columns'])->pluck('id')->filter();
        if (count($incoming) > 0) {
            $board->columns()
                ->whereNotIn('id', $incoming)
                ->delete();
        }
        foreach ($data['columns'] as $col) {

            if ($col['id'] === 0) {
                $board->columns()
                    ->create([
                        'name' => $col['name'],
                        'color' => $col['color']
                    ]);
            }
            $board->columns()
                ->where('id', $col['id'])
                ->update(['name' => $col['name']]);
        }

        return response()->json($board->load('columns'));
    }
    public function clearBoard($id)
    {
        $board = Board::find($id);

        if (!$board) {
            return response()->json(['message' => 'Board not found'], 404);
        }

        $board->columns()->delete();

        return response()->json($board->load('columns'));
    }
    public function destroy(Board $board)
    {
        $board->delete();
        return response()->json(['message' => 'Board eliminada con exito']);
    }
    public function resetBoards()
    {
        Column::query()->delete();
        $boards = Board::with('columns.tasks')->get();

        return response()->json($boards);
    }
}
