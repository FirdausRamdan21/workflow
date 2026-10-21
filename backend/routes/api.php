<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::middleware('api')->group(function () {
    // Summary endpoint (must be before apiResource to avoid route conflict)
    Route::get('tasks/summary', [TaskController::class, 'summary']);
    
    // Task CRUD endpoints
    Route::apiResource('tasks', TaskController::class);
});
