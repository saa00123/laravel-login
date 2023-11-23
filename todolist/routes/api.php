<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;
use App\Htpp\Controllers\AdminController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{user}/todos', [TodoController::class, 'indexForUser']);
    Route::post('/user/{user}/todos', [TodoController::class, 'storeForUser']);
    Route::put('/user/{user}/todos/{todo}', [TodoController::class, 'update']);
    Route::delete('/user/{user}/todos/{todo}', [TodoController::class, 'destroy']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

Route::middleware('auth:api', 'is_admin')->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index']);
    Route::apiResource('admin/todos', AdminController::class);
});