<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            if (!Schema::hasColumn('user', 'is_admin')) {
                $table->boolean('is_admin')->default(false);
            }
        });
    }
    
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
        });
    }
};
