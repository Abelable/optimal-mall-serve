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
            $table->integer('gender')->default(0)->comment('性别：0-未知，1-男，2-女');
            $table->string('bg')->default('')->comment('背景图');
            $table->string('birthday')->default('')->comment('生日');
            $table->string('constellation')->default('')->comment('星座');
            $table->string('career')->default('')->comment('职业');
            $table->string('signature')->default('')->comment('签名');
            $table->timestamps();
            $table->softDeletes();
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
