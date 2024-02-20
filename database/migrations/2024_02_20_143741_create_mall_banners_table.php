<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMallBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_banners', function (Blueprint $table) {
            $table->id();
            $table->string('cover')->comment('活动封面');
            $table->string('desc')->default('')->comment('活动描述');
            $table->integer('scene')->comment('链接跳转场景值：1-h5活动，2-景点详情，3-酒店详情，4-餐饮门店详情， 5-商品详情');
            $table->string('value')->comment('链接参数值');
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
        Schema::dropIfExists('mall_banners');
    }
}
