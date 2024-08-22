<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\UserInfoInput;
use App\Utils\TimServe;

class UserController extends Controller
{
    public function userInfo()
    {
        $user = $this->user();

        $promoter = PromoterService::getInstance()->getPromoterByUserId($user->id);
        $user['level'] = $promoter ? $promoter->level : 0;

        unset($user->openid);
        unset($user->created_at);
        unset($user->updated_at);

        return $this->success($user);
    }

    public function updateUserInfo()
    {
        /** @var UserInfoInput $input */
        $input = UserInfoInput::new();
        $user = $this->user();

        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        if (!empty($input->wxQrcode)) {
            $user->wx_qrcode = $input->wxQrcode;
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

    public function superiorInfo()
    {
        $superiorId = $this->verifyRequiredId('superiorId');

        $superiorInfo = UserService::getInstance()->getUserById($superiorId, ['id', 'avatar', 'nickname', 'gender', 'wx_qrcode', 'signature']);
        if (is_null($superiorInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前上级用户不存在');
        }

        return $this->success($superiorInfo);
    }
}
