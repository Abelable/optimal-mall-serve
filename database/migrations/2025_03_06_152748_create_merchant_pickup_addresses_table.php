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
            $table->decimal('longitude', 9, 6)->default(0.000000)->comment('提货点经度');
            $table->decimal('latitude', 8, 6)->default(0.000000)->comment('提货点纬度');
            $table->string('address_detail')->default('')->comment('提货点地址');
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
