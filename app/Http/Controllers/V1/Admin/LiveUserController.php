<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveUser;
use App\Services\LiveUserService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class LiveUserController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LiveUserService::getInstance()->getUserPage($input);
        return $this->successPaginate($page);
    }

    public function add()
    {
        $userIds = $this->verifyArrayNotEmpty('userIds');

        $liveUserList = LiveUserService::getInstance()->getFilterUserList($userIds);
        if (count($liveUserList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '直播人员已存在，请勿重复添加');
        }

        $userList = UserService::getInstance()->getListByIds($userIds);

        foreach ($userList as $user) {
            $liveUser = LiveUser::new();
            $liveUser->user_id = $user->id;
            $liveUser->avatar = $user->avatar;
            $liveUser->nickname = $user->nickname;
            $liveUser->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $liveUser = LiveUserService::getInstance()->getUserById($id);
        if (is_null($liveUser)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前直播人员不存在');
        }
        $liveUser->delete();
        return $this->success();
    }
}
