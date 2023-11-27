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
            if (!Schema::hasColumn('user', 'create_allowed')) {
                $table->boolean('create_allowed')->default(true);
            }
            if (!Schema::hasColumn('user', 'update_allowed')) {
                $table->boolean('update_allowed')->default(true);
            }
            if (!Schema::hasColumn('user', 'delete_allowed')) {
                $table->boolean('delete_allowed')->default(true);
            }
        });
    }
    
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            if (Schema::hasColumn('user', 'create_allowed')) {
                $table->dropColumn('create_allowed');
            }
            if (Schema::hasColumn('user', 'update_allowed')) {
                $table->dropColumn('update_allowed');
            }
            if (Schema::hasColumn('user', 'delete_allowed')) {
                $table->dropColumn('delete_allowed');
            }
        });
    }
};

