<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_evaluations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->default(0)->comment('订单id');
            $table->integer('goods_id')->comment('商品id');
            $table->float('score')->comment('商品评分');
            $table->string('content')->comment('评论内容');
            $table->longText('image_list')->comment('评论图片');
            $table->integer('like_number')->default(0)->comment('点赞数');
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
        Schema::dropIfExists('goods_evaluations');
    }
}
