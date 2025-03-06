<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantRefundAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_refund_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('merchant_id')->comment('商家id');
            $table->string('consignee_name')->comment('收件人姓名');
            $table->string('mobile')->comment('手机号');
            $table->string('address_detail')->comment('收件地址');
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
        Schema::dropIfExists('merchant_refund_addresses');
    }
}
