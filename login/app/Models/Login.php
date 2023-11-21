<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = 'login';
    protected $fillable = ['name', 'nickname', 'password', 'email', 'phone'];
}