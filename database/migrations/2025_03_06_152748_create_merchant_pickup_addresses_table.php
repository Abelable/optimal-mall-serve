<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPickupAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_pickup_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('merchant_id')->comment('商家id');
            $table->string('name')->comment('提货点名称');
            $table->string('time_frame')->default('')->comment('提货时间范围');
            $table->string('address_detail')->comment('提货点地址');
            $table->decimal('longitude', 9, 6)->comment('提货点经度');
            $table->decimal('latitude', 8, 6)->comment('提货点纬度');
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
        Schema::dropIfExists('merchant_pickup_addresses');
    }
}
