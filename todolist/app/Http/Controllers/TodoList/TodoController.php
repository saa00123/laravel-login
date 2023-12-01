<?php

namespace App\Http\Controllers\TodoList;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     * 특정 사용자의 할 일 목록을 조회하는 메소드
     * @param int $userId 사용자 ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexForUser($userId)
    {
        try {
            $user = User::find($userId);
    
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $permissions = [
                'create_allowed' => $user->create_allowed,
                'update_allowed' => $user->update_allowed,
                'delete_allowed' => $user->delete_allowed,
            ];

            return response()->json([
                'todos' => $user->todos,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
