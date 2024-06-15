<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('商品状态：1-销售中，2-下架');
            $table->integer('category_id')->comment('商品分类id');
            $table->integer('merchant_id')->default(0)->comment('商家id');
            $table->string('cover')->comment('商品封面');
            $table->longText('image_list')->comment('主图图片列表');
            $table->longText('detail_image_list')->comment('详情图片列表');
            $table->string('default_spec_image')->comment('默认规格图片');
            $table->string('name')->comment('商品名称');
            $table->integer('freight_template_id')->default(0)->comment('运费模板id：0-包邮');
            $table->float('price')->comment('商品价格');
            $table->float('market_price')->default(0)->comment('市场价格');
            $table->integer('stock')->comment('商品库存');
            $table->float('leader_commission_rate')->comment('团队长佣金比例%');
            $table->float('share_commission_rate')->comment('分享佣金比例%');
            $table->longText('spec_list')->comment('商品规格列表');
            $table->longText('sku_list')->comment('商品sku');
            $table->integer('sales_volume')->default(0)->comment('商品销量');
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
        Schema::dropIfExists('goods');
    }
}
