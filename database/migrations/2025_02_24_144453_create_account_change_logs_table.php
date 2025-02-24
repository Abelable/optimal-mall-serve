<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_change_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->comment('账户id');
            $table->float('old_balance')->comment('变更前余额');
            $table->float('new_balance')->comment('变更后余额');
            $table->float('change_amount')->comment('变更金额');
            $table->integer('change_type')->comment('变更类型：1-佣金提现，2-消费，3-退款');
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
        Schema::dropIfExists('account_change_logs');
    }
}
