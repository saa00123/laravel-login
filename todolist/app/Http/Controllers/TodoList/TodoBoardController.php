<?php

namespace App\Http\Controllers\TodoList;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoBoardController extends Controller
{
    /**
     * 모든 사용자의 Todo 목록을 조회하는 메소드
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTodos()
    {
        $todos = Todo::with('user')->get(); // 사용자 정보와 함께 Todo 목록을 가져옵니다.
        return response()->json($todos);
    }
}
