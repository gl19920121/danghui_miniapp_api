<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_id')->comment('职位id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->enum('type', ['collect'])->comment('操作行为');
            $table->integer('times')->nullable()->default(1)->comment('操作次数');
            $table->timestamps();

            $table->unique(['job_id', 'user_id', 'type']);
            // $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_user');
    }
}
