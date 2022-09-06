<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nickname')->comment('用户昵称或网络名称');
            $table->string('avatar')->comment('用户头像图片');
            $table->string('mobile')->unique()->comment('用户手机号码');
            $table->string('openid')->unique()->comment('小程序openid');
            $table->string('unionid')->unique()->comment('微信unionid');
            $table->integer('gender')->default(0)->comment('性别：0-未知，1-男，2-女');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `users` comment '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
