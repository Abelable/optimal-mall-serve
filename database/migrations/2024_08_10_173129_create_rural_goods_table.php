<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuralGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rural_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('region_id')->comment('地区id');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('goods_cover')->comment('商品图片');
            $table->integer('goods_name')->comment('商品名称');
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
        Schema::dropIfExists('rural_goods');
    }
}
