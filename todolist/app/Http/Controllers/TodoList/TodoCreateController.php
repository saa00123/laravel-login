<?php

namespace App\Http\Controllers\TodoList;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoCreateController extends Controller
{
      /**
     * 특정 사용자에 대한 새로운 할 일을 추가하는 메소드
     * @param Request $request 요청 데이터
     * @param int $userId 사용자 ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeForUser(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$user->create_allowed) {
            return response()->json(['error' => 'Action not allowed'], 401);
        }

        $todo = new Todo($request->all());
        $todo->user_id = $userId; 
        $user->todos()->save($todo);

        return $todo;
    }
}