<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::withCount('todos')->get(['id', 'name', 'todos_count']);

        return response()->json($users);
    }
    public function toggleCrudPermission(Request $request, User $user)
    {
        $user->save();

        return response()->json(['success' => true]);
    }
    
    public function updatePermissions(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->create_allowed = $request->input('create_allowed', $user->create_allowed);
        $user->update_allowed = $request->input('update_allowed', $user->update_allowed);
        $user->delete_allowed = $request->input('delete_allowed', $user->delete_allowed);
        $user->save();

        return response()->json(['success' => true]);
    }
}
