<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveRoom;
use App\Services\LiveRoomService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class LiveRoomController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LiveRoomService::getInstance()->adminPageList($input);
        $liveRoomList = collect($page->items());

        $userIds = $liveRoomList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])
            ->keyBy('id');

        $list = $liveRoomList->map(function (LiveRoom $liveRoom) use ($userList) {
            $user = $userList->get($liveRoom->user_id);
            $liveRoom['userInfo'] = $user;
            unset($liveRoom->user_id);

            return $liveRoom;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');

        $live = LiveRoomService::getInstance()->getRoomById($id);
        if (is_null($live)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前直播不存在');
        }

        $live->views = $views;
        $live->save();

        return $this->success();
    }

    public function editPraise()
    {
        $id = $this->verifyRequiredId('id');
        $praise = $this->verifyRequiredInteger('praise');

        $live = LiveRoomService::getInstance()->getRoomById($id);
        if (is_null($live)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前直播不存在');
        }

        $live->praise_number = $praise;
        $live->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $live = LiveRoomService::getInstance()->getRoomById($id);
        if (is_null($live)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前直播不存在');
        }
        $live->delete();
        return $this->success();
    }
}
