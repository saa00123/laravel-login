<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all(['id', 'name']);
        return response()->json($users);
    }

    public function toggleCrudPermission(Request $request, User $user)
{
    $user->is_crud_allowed = !$user->is_crud_allowed;
    $user->save();

    return response()->json(['success' => true]);
}
}
