<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('优惠券状态：1-有效，2-过期，3-下架');
            $table->string('name')->comment('优惠券名称');
            $table->float('denomination')->comment('优惠券面额');
            $table->string('description')->comment('优惠券说明');
            $table->integer('type')->comment('优惠券类型：1-无门槛，2-商品数量满减，3-价格满减');
            $table->integer('num_limit')->default(0)->comment('优惠券商品数量门槛');
            $table->float('price_limit')->default(0)->comment('优惠券价格门槛');
            $table->string('expiration_time')->default('')->comment('优惠券失效时间');
            $table->integer('goods_id')->default(0)->comment('商品id');
            $table->string('goods_cover')->default('')->comment('商品图片');
            $table->string('goods_name')->default('')->comment('商品名称');
            $table->integer('received_num')->default(0)->comment('优惠券领取数量');
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
        Schema::dropIfExists('coupons');
    }
}
