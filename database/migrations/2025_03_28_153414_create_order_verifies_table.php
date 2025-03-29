<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderVerifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_verifies', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('核销状态：0-待核销，1-已核销');
            $table->integer('order_id')->comment('订单id');
            $table->string('verify_code')->unique()->comment('核销码');
            $table->integer('verifier_id')->default(0)->comment('核销人id');
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
        Schema::dropIfExists('order_verifies');
    }
}
