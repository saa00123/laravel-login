<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user'; // 데이터베이스의 테이블 지정

    /**
     * 대입 가능한 속성들
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'create_allowed',
        'update_allowed',
        'delete_allowed',
        'is_online',
    ];

    /**
     * JSON에 포함되지 않는 속성들
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 속성 형변환
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * 기본 속성 값 설정
     * @var array
     */
    protected $attributes = [
        'create_allowed' => true,
        'update_allowed' => true,
        'delete_allowed' => true,
        'is_online' => false,
    ];

    /**
     * 사용자가 가진 할 일 관계 정의
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    /**
     * 사용자의 온라인 상태 변경 및 브로드캐스팅
     * @param bool $value 온라인 상태 값
     */
    public function setOnline($value)
    {
        $this->is_online = $value;
        $this->save();

        broadcast(new UserOnlineStatusChanged($this))->toOthers();
    }
}
