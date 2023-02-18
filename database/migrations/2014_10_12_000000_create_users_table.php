<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('code', 6)->unique();
                $table->string('name', 100);
                $table->string('password');
                $table->integer('error_count')->default(0)->unsigned();
                $table->string('role')->nullable();
                $table->tinyInteger('locked_flg')->default(0);
                $table->tinyInteger('delete_flg')->default(0);
                $table->timestamp('updated_at')->useCurrent()->nullable();
                $table->timestamp('created_at')->useCurrent()->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
