<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMouldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moulds', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('模板类型：0->默认，1->有图有文，图文居下，左图右文');
            $table->string('image')->nullable()->comment('图片地址');
            $table->text('text')->nullable()->comment('文字内容');
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
        Schema::dropIfExists('moulds');
    }
}
