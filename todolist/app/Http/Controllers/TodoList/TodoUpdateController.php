<?php

namespace App\Http\Controllers\TodoList;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoUpdateController extends Controller
{
      /**
     * 특정 사용자의 특정 할 일을 업데이트하는 메소드
     * @param Request $request 요청 데이터
     * @param int $userId 사용자 ID
     * @param int $todoId 할 일 ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $userId, $todoId)
    {
        try {
            $todo = Todo::where('user_id', $userId)->where('id', $todoId)->firstOrFail();
            $todo->update($request->all());
            return response()->json($todo);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}