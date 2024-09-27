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
            $table->integer('status')->default(1)->comment('商品状态：1-销售中，2-下架');
            $table->integer('merchant_id')->default(0)->comment('商家id');
            $table->string('video')->default('')->comment('商品视频');
            $table->string('cover')->comment('商品封面');
            $table->string('activity_cover')->default('')->comment('活动封面');
            $table->longText('image_list')->comment('主图图片列表');
            $table->longText('detail_image_list')->comment('详情图片列表');
            $table->string('default_spec_image')->comment('默认规格图片');
            $table->string('name')->comment('商品名称');
            $table->string('introduction')->default('')->comment('商品介绍');
            $table->integer('freight_template_id')->default(0)->comment('运费模板id：0-包邮');
            $table->float('price')->comment('起始价格');
            $table->float('market_price')->default(0)->comment('市场原价');
            $table->integer('stock')->comment('商品库存');
            $table->float('commission_rate')->default(0)->comment('佣金比例%');
            $table->integer('refund_status')->default(0)->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->longText('spec_list')->comment('商品规格列表');
            $table->longText('sku_list')->comment('商品sku');
            $table->integer('sales_volume')->default(0)->comment('商品销量');
            $table->float('avg_score')->default(0)->comment('综合评分');
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
