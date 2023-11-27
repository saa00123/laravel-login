<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
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

    public function update(Request $request, $userId, Todo $todo)
    {
        if ($todo->user_id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!$user->update_allowed) {
            return response()->json(['error' => 'Action not allowed'], 401);
        }

        $todo->update($request->all());
        return $todo;
    }

    public function destroy($userId, Todo $todo)
    {
        if ($todo->user_id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!$user->delete_allowed) {
            return response()->json(['error' => 'Action not allowed'], 401);
        }

        $todo->delete();
        return response()->json(null, 204);
    }
}

