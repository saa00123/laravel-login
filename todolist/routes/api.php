<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']); 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/{user}/todos', [TodoController::class, 'indexForUser']);
    Route::post('/user/{user}/todos', [TodoController::class, 'storeForUser']);
    Route::put('/user/{user}/todos/{todo}', [TodoController::class, 'update']);
    Route::delete('/user/{user}/todos/{todo}', [TodoController::class, 'destroy']);

    Route::middleware('is_admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index']);
        Route::put('/admin/user/{user}/permissions', [AdminController::class, 'updatePermissions']);
    });
});
