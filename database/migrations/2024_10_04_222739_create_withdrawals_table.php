<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('type')->comment('佣金类型：1-商品自购佣金；2-商品分享佣金；3-礼包佣金');
            $table->float('withdraw_amount')->comment('提现金额');
            $table->float('tax_fee')->default(0)->comment('税费');
            $table->float('handling_fee')->default(1)->comment('手续费');
            $table->float('actual_amount')->comment('实际到账金额');
            $table->integer('path')->comment('提现方式：1-微信；2-银行卡');
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
        Schema::dropIfExists('withdrawals');
    }
}