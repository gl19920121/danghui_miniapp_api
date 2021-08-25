<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIntentionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_intentions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['full', 'part', 'all'])->comment('求职类型');
            $table->string('city')->comment('工作城市');
            $table->json('position')->comment('期望职位');
            $table->json('industry')->nullable()->comment('期望行业');
            $table->json('salary')->comment('薪资要求');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade')->comment('用户ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_intentions');
    }
}
