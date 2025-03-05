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
            $table->string('name')->comment('商家名称');
            $table->string('consignee_name')->comment('收货人姓名');
            $table->string('mobile')->comment('手机号');
            $table->string('address_detail')->comment('收获地址');
            $table->longText('license')->comment('经营资质');
            $table->decimal('longitude', 9, 6)->default(0.000000)->comment('提货点经度');
            $table->decimal('latitude', 8, 6)->default(0.000000)->comment('提货点纬度');
            $table->string('pickup_address_detail')->default('')->comment('提货点地址');
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
