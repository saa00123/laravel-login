<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained();  // 방 ID (외래키)
            $table->foreignId('user_id')->constrained();  // 사용자 ID (외래키)
            $table->dateTime('start_date');  // 시작 날짜
            $table->dateTime('end_date');    // 종료 날짜
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
