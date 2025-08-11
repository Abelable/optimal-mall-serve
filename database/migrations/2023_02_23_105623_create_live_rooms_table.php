<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('直播创建者id');
            $table->integer('status')->default(0)->comment('直播状态：0-待开播(预告)，1-直播中，2-直播结束, 3-直播预告');
            $table->string('title')->comment('直播标题');
            $table->string('cover')->comment('直播封面');
            $table->string('share_cover')->comment('直播间分享封面');
            $table->integer('direction')->comment('方向：1-竖屏，2-横屏');
            $table->string('push_url')->default('')->comment('推流地址');
            $table->string('play_url')->default('')->comment('拉流地址');
            $table->string('playback_url')->default('')->comment('回放地址');
            $table->string('group_id')->default('')->comment('群聊群组id');
            $table->integer('views')->default(0)->comment('观看人数');
            $table->integer('praise_number')->default(0)->comment('点赞数');
            $table->string('notice_time')->default('')->comment('预告时间');
            $table->string('start_time')->default('')->comment('开播时间');
            $table->string('end_time')->default('')->comment('结束时间');
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
        Schema::dropIfExists('live_rooms');
    }
}
