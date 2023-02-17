<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->comment('订单id');
            $table->string('image')->comment('列表图片');
            $table->string('name')->comment('商品名称');
            $table->float('price')->comment('商品价格');
            $table->string('selected_sku_name')->comment('选中的规格名称');
            $table->integer('selected_sku_index')->comment('选中的规格索引');
            $table->integer('number')->comment('商品数量');
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
        Schema::dropIfExists('order_goods');
    }
}
