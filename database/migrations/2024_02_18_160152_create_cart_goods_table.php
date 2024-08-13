<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('scene')->default(1)->comment('场景值：1-添加购物车，2-直接购买');
            $table->integer('status')->default(1)
                ->comment('购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除');
            $table->string('status_desc')->default('')->comment('购物车商品状态描述');
            $table->integer('user_id')->comment('用户id');
            $table->integer('goods_id')->comment('商品id');
            $table->string('category_ids')->comment('商品分类');
            $table->integer('freight_template_id')->comment('运费模板id');
            $table->string('cover')->comment('商品图片');
            $table->string('name')->comment('商品名称');
            $table->string('selected_sku_name')->default('')->comment('选中的规格名称');
            $table->integer('selected_sku_index')->default(-1)->comment('选中的规格索引');
            $table->float('price')->comment('商品价格');
            $table->float('market_price')->default(0)->comment('市场价格');
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
        Schema::dropIfExists('cart_goods');
    }
}
