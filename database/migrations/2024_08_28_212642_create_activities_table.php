<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->comment('活动状态：0-活动预告，1-活动进行中，2-活动结束');
            $table->string('name')->comment('活动名称');
            $table->string('start_time')->default('')->comment('活动开始时间');
            $table->string('end_time')->default('')->comment('活动结束时间');
            $table->integer('goods_type')->comment('商品类型：1-农产品，2-爆品');
            $table->integer('goods_id')->comment('商品id');
            $table->string('goods_cover')->comment('商品图片');
            $table->string('goods_name')->comment('商品名称');
            $table->integer('followers')->default(0)->comment('活动关注数');
            $table->integer('sales')->default(0)->comment('活动销量');
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
        Schema::dropIfExists('activities');
    }
}
