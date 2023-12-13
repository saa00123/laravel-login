<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['title', 'user_id', 'completed'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}