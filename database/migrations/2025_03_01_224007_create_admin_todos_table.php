<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_todos', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('类型：1-订单待发货，2-售后，3-实名认证，4-企业认证，5-佣金提现');
            $table->string('reference_id')->default('')->comment('外部参考ID，如订单ID');
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
        Schema::dropIfExists('admin_todos');
    }
}
