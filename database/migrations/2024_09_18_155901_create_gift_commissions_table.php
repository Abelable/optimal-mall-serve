<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('佣金状态：0-订单待支付，1-待结算, 2-可提现，3-已结算');
            $table->integer('user_id')->comment('用户id');
            $table->integer('promoter_id')->default(0)->comment('推广员id');
            $table->integer('manager_id')->default(0)->comment('组织者id');
            $table->integer('order_id')->comment('订单id');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('refund_status')->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->string('selected_sku_name')->default('')->comment('选中的规格名称');
            $table->float('goods_price')->comment('商品价格');
            $table->integer('promoter_commission_rate')->default(0)->comment('推广员佣金比例%');
            $table->integer('manager_commission_rate')->default(0)->comment('组织者佣金比例%');
            $table->float('promoter_commission')->default(0)->comment('推广员佣金');
            $table->float('manager_commission')->default(0)->comment('组织者佣金');
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
        Schema::dropIfExists('gift_commissions');
    }
}
