<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('佣金状态：0-订单待支付，1-待结算, 2-已结算，3-已提现');
            $table->integer('scene')->comment('场景：1-自购，2-分享');
            $table->integer('user_id')->comment('用户id');
            $table->integer('superior_id')->default(0)->comment('上级id');
            $table->integer('order_id')->comment('订单id');
            $table->integer('goods_id')->comment('商品id');
            $table->float('payment_amount')->comment('商品支付金额');
            $table->float('commission_rate')->comment('商品佣金比例');
            $table->float('commission')->comment('佣金金额');
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
        Schema::dropIfExists('commissions');
    }
}
