<?php

namespace App\Http\Controllers\TodoList;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TodoCreateController extends Controller
{
      /**
     * 특정 사용자에 대한 새로운 할 일을 추가하는 메소드
     * @param Request $request 요청 데이터
     * @param int $userId 사용자 ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeForUser(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'completed' => ['boolean'],
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$user->create_allowed) {
            return response()->json(['error' => 'Action not allowed'], 401);
        }

        // $todo = new Todo($request->all());
        // $todo->user_id = $userId; 
        // $user->todos()->save($todo);
        // dd($request->user());

        $todoData = [
            'title' => $data['title'],
            'user_id' => $user->id,
        ];

        if(isset($data['completed'])) {
            $todoData['completed'] = $data['completed'];
        }
        // 1
        $todo = Todo::create($todoData);

        // 2
        // $user->todos()->create([
        //     'title' => $data['title'],
        //     'completed' => $data['completed'],
        // ]);
        
        return $todo;
    }
}