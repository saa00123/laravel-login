<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// 사용자 인증 관련 라우트
Route::post('/register', [AuthController::class, 'register']); // 사용자 등록
Route::post('/login', [AuthController::class, 'login']); // 로그인
Route::post('/logout', [AuthController::class, 'logout']); // 로그아웃

// 인증된 사용자만 접근 가능한 라우트 그룹
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']); // 현재 인증된 사용자 정보 조회
    Route::get('/user/{user}/todos', [TodoController::class, 'indexForUser']); // 특정 사용자의 할 일 목록 조회
    Route::post('/user/{user}/todos', [TodoController::class, 'storeForUser']); // 특정 사용자에게 할 일 추가
    Route::put('/user/{user}/todos/{todo}', [TodoController::class, 'update']); // 특정 할 일 수정
    Route::delete('/user/{user}/todos/{todo}', [TodoController::class, 'destroy']); // 특정 할 일 삭제

    // 관리자 전용 라우트 그룹
    Route::middleware('is_admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index']); // 관리자 대시보드
        Route::put('/admin/user/{user}/permissions', [AdminController::class, 'updatePermissions']); // 사용자 권한 업데이트
    });
});
