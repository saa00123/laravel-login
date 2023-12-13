<?php

namespace App\Http\Controllers\TodoList;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TodoDeleteController extends Controller
{
      /**
     * 특정 사용자의 특정 할 일을 삭제하는 메소드
     * @param int $userId 사용자 ID
     * @param int $todoId 할 일 ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($userId, $todoId)
    {
        try {
            $todo = Todo::query()
                ->where('user_id', $userId)
                ->where('id', $todoId)
                ->firstOrFail();
            $todo->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}