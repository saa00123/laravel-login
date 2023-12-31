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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->boolean('completed')->default(false);
            $table->timestamps();
            // $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('todos');
    }
}
