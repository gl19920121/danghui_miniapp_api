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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('openid');
            $table->string('session_key');
            $table->string('password')->comment('登录密码');
            $table->string('unionid')->nullable();
            $table->string('phone')->nullable()->comment('手机号');
            $table->string('nick_name')->nullable()->comment('用户昵称');
            $table->string('avatar_url')->nullable()->comment('用户头像');
            $table->enum('gender', ['0', '1', '2'])->nullable()->comment('用户性别');
            $table->string('country')->nullable()->comment('用户所在国家

');
            $table->string('province')->nullable()->comment('用户所在省份

');
            $table->string('city')->nullable()->comment('用户所在城市

');
            $table->enum('language', ['en', 'zh_CN', 'zh_TW'])->nullable()->comment('显示 country，province，city 所用的语言

');
            $table->timestamps();
            $table->unique('openid');
        });
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
