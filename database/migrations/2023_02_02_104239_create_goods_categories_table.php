<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状态: 1-显示,2-隐藏');
            $table->string('name')->comment('商品分类名称');
            $table->integer('sort')->default(1)->comment('排序');
            $table->integer('min_leader_commission_rate')->comment('最小团队长佣金比例');
            $table->integer('max_leader_commission_rate')->comment('最大团队长佣金比例');
            $table->integer('min_share_commission_rate')->comment('最小分享佣金比例');
            $table->integer('max_share_commission_rate')->comment('最大分享佣金比例');
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
        Schema::dropIfExists('goods_categories');
    }
}
