<?php

namespace App\Observers;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        try {
            Mail::raw("안녕하세요, {$user->name}님! 회원 가입을 환영합니다.", function ($message) use ($user) {
                $message->to($user->email)->subject('가입 환영');
            });
        } catch (\Exception $e) {
            \Log::error('메일 전송 실패: ' . $e->getMessage());
        }
    }
}
