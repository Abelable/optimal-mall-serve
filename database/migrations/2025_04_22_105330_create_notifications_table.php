<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('消息状态：0-未读，1-已读');
            $table->integer('type')->default(0)->comment('消息类型');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->string('cover')->default('')->comment('消息封面');
            $table->string('title')->comment('消息标题');
            $table->string('content')->comment('消息内容');
            $table->integer('content_num')->default(1)->comment('消息内容数量');
            $table->string('reference_id')->default('')->comment('外部参考ID，如订单号');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
