<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::prefix('v1')->group(function() {
    Route::post('registration', [AuthController::class, 'registration']);
    Route::post('authorization', [AuthController::class, 'authorization']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('tasks', TaskController::class);
    });
});
