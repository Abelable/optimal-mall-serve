<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemeZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_zones', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状态: 1-显示,2-隐藏');
            $table->string('name')->comment('主题名称');
            $table->string('cover')->comment('主题封面');
            $table->string('bg')->default('')->comment('主题背景');
            $table->integer('scene')->default(1)
                ->comment('链接跳转场景值：1-主题商品页，2-h5活动页，3-原生活动页');
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
        Schema::dropIfExists('theme_zones');
    }
}
