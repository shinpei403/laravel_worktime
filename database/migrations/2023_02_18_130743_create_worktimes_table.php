<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorktimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('worktimes')) {
            Schema::create('worktimes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('date');
                $table->time('start_time');
                $table->time('end_time');
                $table->time('break_time');
                $table->timestamp('updated_at')->useCurrent()->nullable();
                $table->timestamp('created_at')->useCurrent()->nullable();

                $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('worktimes');
    }
}
