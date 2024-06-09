<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreightTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freight_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('模板名称');
            $table->string('title')->comment('模板标题，可展示在商品详情页');
            $table->integer('compute_mode')->default(1)->comment('计算方式：1-不计重量和件数，2-按商品件数');
            $table->float('free_quota')->default(0)->comment('免费额度');
            $table->longText('area_list')->comment('配送地区列表');
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
        Schema::dropIfExists('freight_templates');
    }
}
