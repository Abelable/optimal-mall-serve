<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('room_id')->comment('直播间id');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('is_hot')->default(0)->comment('是否正在热卖：0-非热卖，1-热卖中');
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
        Schema::dropIfExists('live_goods');
    }
}
