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
            $table->string('order_sn')->unique()->comment('订单编号');
            $table->integer('status')->comment('订单状态');
            $table->integer('user_id')->comment('用户id');
            $table->integer('delivery_mode')->default(1)->comment('配送方式：1-快递，2-自提');
            $table->string('consignee')->default('')->comment('收件人姓名');
            $table->string('mobile')->default('')->comment('收件人手机号');
            $table->string('address')->default('')->comment('具体收货地址');
            $table->integer('pickup_address_id')->default(0)->comment('提货地址id');
            $table->string('pickup_time')->default('')->comment('提货时间');
            $table->string('pickup_mobile')->default('')->comment('提货预留手机号');
            $table->integer('merchant_id')->comment('商家id');
            $table->float('goods_price')->comment('商品总价格');
            $table->float('freight_price')->comment('运费');
            $table->integer('coupon_id')->default(0)->comment('优惠券id');
            $table->float('coupon_denomination')->default(0)->comment('优惠券抵扣');
            $table->float('deduction_balance')->default(0)->comment('余额抵扣');
            $table->float('payment_amount')->comment('支付金额');
            $table->float('total_payment_amount')->default(0)->comment('总支付金额，拆单场景');
            $table->string('pay_id')->default('')->comment('支付id');
            $table->string('pay_time')->default('')->comment('支付时间');
            $table->string('ship_channel')->default('')->comment('快递公司');
            $table->string('ship_code')->default('')->comment('快递公司编号');
            $table->string('ship_sn')->default('')->comment('快递编号');
            $table->string('ship_time')->default('')->comment('发货时间');
            $table->string('confirm_time')->default('')->comment('确认收货/使用时间');
            $table->string('finish_time')->default('')->comment('订单关闭时间');
            $table->float('refund_amount')->comment('退款金额');
            $table->string('refund_id')->default('')->comment('退款id');
            $table->string('refund_type')->default('')->comment('退款方式');
            $table->string('refund_remarks')->default('')->comment('退款备注');
            $table->string('refund_time')->default('')->comment('退款时间');
            $table->string('remarks')->default('')->comment('订单备注');
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
