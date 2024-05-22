<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_sn')->comment('订单编号');
            $table->integer('status')->comment('订单状态');
            $table->string('remarks')->default('')->comment('订单备注');
            $table->integer('user_id')->comment('用户id');
            $table->string('consignee')->comment('收件人姓名');
            $table->string('mobile')->comment('收件人手机号');
            $table->string('address')->comment('具体收货地址');
            $table->float('goods_price')->comment('商品总价格');
            $table->float('freight_price')->comment('运费');
            $table->float('payment_amount')->comment('支付金额');
            $table->integer('pay_id')->default(0)->comment('支付id');
            $table->string('pay_time')->default('')->comment('支付时间');
            $table->string('ship_sn')->default('')->comment('发货编号');
            $table->string('ship_channel')->default('')->comment('快递公司');
            $table->string('ship_time')->default('')->comment('发货时间');
            $table->string('confirm_time')->default('')->comment('用户确认收货时间');
            $table->string('finish_time')->default('')->comment('订单关闭时间');
            $table->float('refund_amount')->comment('退款金额');
            $table->string('refund_type')->default('')->comment('退款方式');
            $table->string('refund_remarks')->default('')->comment('退款备注');
            $table->string('refund_time')->default('')->comment('退款时间');
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
        Schema::dropIfExists('orders');
    }
}
