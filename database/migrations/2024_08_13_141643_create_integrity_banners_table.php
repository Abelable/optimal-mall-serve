<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrityBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integrity_banners', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('活动状态：1-活动中，2-活动结束');
            $table->string('cover')->comment('活动封面');
            $table->string('desc')->default('')->comment('活动描述');
            $table->string('scene')->default(0)->comment('链接跳转场景值：1-h5活动，2-商品详情');
            $table->string('param')->default('')->comment('链接参数值');
            $table->integer('sort')->default(1)->comment('排序');
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
        Schema::dropIfExists('integrity_banners');
    }
}
