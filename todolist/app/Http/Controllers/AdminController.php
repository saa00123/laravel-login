<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * 사용자 목록과 각 사용자의 할 일 수를 조회하는 메소드
     */
    public function index()
    {
        $users = User::withCount('todos')
                     ->get(['id', 'name', 'todos_count', 'is_online']);

        return response()->json($users);
    }

    /**
     * 특정 사용자의 권한을 업데이트하는 메소드
     * 
     * @param Request $request 요청 데이터
     * @param int $userId 사용자 ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePermissions(Request $request, $userId)
    {
        $user = User::find($userId);
        // 사용자가 존재하지 않는 경우 오류 메시지 반환
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // 요청에서 받은 권한 값으로 사용자 권한 업데이트
        $user->create_allowed = $request->input('create_allowed', $user->create_allowed);
        $user->update_allowed = $request->input('update_allowed', $user->update_allowed);
        $user->delete_allowed = $request->input('delete_allowed', $user->delete_allowed);
        $user->save();

        return response()->json(['success' => true]);
    }
}
