<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveRoom;
use App\Services\LiveRoomService;
use App\Services\UserService;
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
        $userList = UserService::getInstance()->getListByIds($userIds)->keyBy('id');

        $list = $liveRoomList->map(function (LiveRoom $liveRoom) use ($userList) {
            $user = $userList->get($liveRoom->user_id);
            $liveRoom['userInfo'] = $user;
            unset($liveRoom->user_id);

            return $liveRoom;
        });

        return $this->success($this->paginate($page, $list));
    }
}
