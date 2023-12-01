<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Events\UserOnlineStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
      /**
     * 현재 인증된 사용자 정보 조회 메소드
     * @param Request $request 요청 데이터
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}