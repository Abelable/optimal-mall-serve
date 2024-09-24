<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitySubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->string('openid')->comment('小程序openid');
            $table->integer('activity_id')->comment('用户id');
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
        Schema::dropIfExists('activity_subscriptions');
    }
}
