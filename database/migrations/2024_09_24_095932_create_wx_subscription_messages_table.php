<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxSubscriptionMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_subscription_messages', function (Blueprint $table) {
            $table->id();
            $table->string('template_id')->comment('订阅模板id');
            $table->string('page')->comment('跳转页面');
            $table->string('open_id')->comment('接受者openid');
            $table->string('data')->comment('消息模板内容');
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
        Schema::dropIfExists('wx_subscription_messages');
    }
}
