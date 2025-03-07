<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('品牌名称');
            $table->string('company_name')->default('')->comment('企业名称');
            $table->string('consignee_name')->default('')->comment('企业负责人');
            $table->string('mobile')->default('')->comment('负责人手机号');
            $table->string('address_detail')->default('')->comment('企业地址');
            $table->longText('license')->comment('经营资质');
            $table->string('supplement')->default('')->comment('补充说明');
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
        Schema::dropIfExists('merchants');
    }
}
