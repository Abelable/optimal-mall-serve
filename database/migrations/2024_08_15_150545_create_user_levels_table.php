<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('level')->default(0)->comment('用户等级：0-普通用户，1-乡村推广员，2-乡村组织者C1，3-C2，4-C3，5-乡村振兴委员会');
            $table->integer('scene')->default(0)->comment('场景值，防串改，与等级对应「等级-场景值」：0-0, 1-100, 2-201, 3-202, 4-203, 5-300');
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
        Schema::dropIfExists('user_levels');
    }
}