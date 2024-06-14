<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\UserInfoInput;
use App\Utils\TimServe;

class UserController extends Controller
{
    protected $except = ['authorInfo', 'search'];

    public function userInfo()
    {
        $user = $this->user();
        $user['teamLeaderId'] = $user->teamLeader->id ?? 0;

        unset($user->openid);
        unset($user->created_at);
        unset($user->updated_at);
        unset($user->teamLeader);

        return $this->success($user);
    }

    public function updateUserInfo()
    {
        /** @var UserInfoInput $input */
        $input = UserInfoInput::new();
        $user = $this->user();

        if (!empty($input->bg)) {
            $user->bg = $input->bg;
        }
        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        if (!empty($input->birthday)) {
            $user->birthday = $input->birthday;
        }
        if (!empty($input->constellation)) {
            $user->constellation = $input->constellation;
        }
        if (!empty($input->signature)) {
            $user->signature = $input->signature;
        }

        $user->save();

        return $this->success();
    }

    public function timLoginInfo()
    {
        $timServe = TimServe::new();
        $timServe->updateUserInfo($this->userId(), $this->user()->nickname, $this->user()->avatar);
        $loginInfo = $timServe->getLoginInfo($this->userId());
        return $this->success($loginInfo);
    }

    public function authorInfo()
    {
        $authorId = $this->verifyRequiredId('authorId');

        $authorInfo = UserService::getInstance()->getUserById($authorId, ['id', 'avatar', 'nickname', 'bg', 'gender', 'signature']);
        if (is_null($authorInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前用户不存在');
        }

        return $this->success($authorInfo);
    }
}
