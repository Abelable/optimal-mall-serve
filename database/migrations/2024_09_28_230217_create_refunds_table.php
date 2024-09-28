<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，等待买家寄回，2-买家已寄出，待确认，3-退款成功，4-审核失败');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->comment('订单id');
            $table->string('order_sn')->comment('订单编号');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('coupon_id')->default(0)->comment('优惠券id');
            $table->float('refund_amount')->comment('退款金额');
            $table->integer('refund_type')->comment('售后类型：1-仅退款，2-退货退款');
            $table->string('refund_reason')->comment('退款说明');
            $table->string('image_list')->default('')->comment('图片说明');
            $table->string('ship_code')->default('')->comment('快递公司编号');
            $table->string('ship_sn')->default('')->comment('快递编号');
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
        Schema::dropIfExists('refunds');
    }
}
