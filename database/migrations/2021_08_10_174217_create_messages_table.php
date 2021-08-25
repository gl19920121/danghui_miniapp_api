<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('消息类型：0->推送消息，1->沟通邀请，2->私聊消息');
            $table->foreignId('mould_id')->constrained()->onUpdate('cascade')->onDelete('cascade')->comment('模板ID');
            $table->json('content')->comment('消息内容');
            $table->unsignedInteger('send_uid')->comment('发送用户ID');
            $table->foreignId('accept_uid')->constrained('users')->onUpdate('cascade')->onDelete('cascade')->comment('接收用户ID');
            $table->boolean('is_read')->default(false)->comment('是否已读');
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
        Schema::dropIfExists('messages');
    }
}
