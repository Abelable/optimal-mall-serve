<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamLeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_leaders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核失败');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->string('name')->comment('联系人姓名');
            $table->string('mobile')->comment('手机号');
            $table->string('email')->comment('邮箱');
            $table->string('id_card_number')->comment('身份证号');
            $table->string('id_card_front_photo')->comment('身份证正面照片');
            $table->string('id_card_back_photo')->comment('身份证反面照片');
            $table->string('hold_id_card_photo')->comment('手持身份证照片');
            $table->string('qualification_photo')->comment('团长资质证明');
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
        Schema::dropIfExists('team_leaders');
    }
}
