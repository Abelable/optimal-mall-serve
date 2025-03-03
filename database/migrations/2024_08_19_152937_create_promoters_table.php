<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoters', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('level')->comment('用户等级：1-乡村推广员，2-乡村组织者C1，3-C2，4-C3，5-乡村振兴委员会');
            $table->integer('scene')->comment('场景值，防串改，与等级对应「等级-场景值」：1-100, 2-201, 3-202, 4-203, 5-300');
            $table->integer('path')->comment('生成路径：1-管理后台添加，2-礼包购买，3-限时活动');
            $table->string('goods_ids')->default('')->comment('礼包商品id-用于售后退款删除推广员身份');
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
        Schema::dropIfExists('promoters');
    }
}
