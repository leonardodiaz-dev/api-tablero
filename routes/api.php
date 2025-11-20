<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('boards/first-id', [BoardController::class, 'getFirstIdBoard']);
    Route::delete('boards/clear/{id}', [BoardController::class, 'clearBoard']);
    Route::delete('boards/reset', [BoardController::class, 'resetBoards']);
    Route::resource('boards', BoardController::class);
    Route::get('columns/{id}', [ColumnController::class, 'getColumnsByBoardId']);
    Route::get('tasks/{id}', [TaskController::class, 'getAllTaskByBoard']);
    Route::put('tasks/change/{id}',[TaskController::class,'changeCurrentStatus']);
    Route::put('tasks/change-order',[TaskController::class,'changeOrderTasks']);
    Route::resource('tasks', TaskController::class);
    Route::resource('subtasks',SubtaskController::class);
});
