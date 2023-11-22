<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodosTable extends Migration
{
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade'); // 'users' 대신 'user'로 변경
        });
    }

    public function down()
    {
        Schema::dropIfExists('todos');
    }
}
