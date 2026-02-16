<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/tasks/today', [TaskController::class, 'today']);
    Route::get('/tasks/overdue', [TaskController::class, 'overdue']);
    Route::get('/tasks/client/{clientId}', [TaskController::class, 'byClient']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);


    Route::apiResource('tasks', TaskController::class)->except(['show']);
});
