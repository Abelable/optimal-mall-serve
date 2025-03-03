<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\WxMpRegisterInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected $only = [];

    public function getWxMpUserMobile()
    {
        $code = $this->verifyRequiredString('code');
        $mobile = WxMpServe::new()->getUserPhoneNumber($code);
        return $this->success($mobile);
    }

    public function wxMpRegister()
    {
        /** @var WxMpRegisterInput $input */
        $input = WxMpRegisterInput::new();

        $result = WxMpServe::new()->getUserOpenid($input->code);
        $user = UserService::getInstance()->getByMobile($input->mobile);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_NAME_REGISTERED);
        }

        $token = DB::transaction(function () use ($input, $result) {
            // 用户注册
            $user = UserService::getInstance()->register($result['openid'], $input);


            if (!empty($input->superiorId)) {
                // 绑定上下级
                RelationService::getInstance()->banding($input->superiorId, $user->id);

                // 增加上级推广人数
                PromoterService::getInstance()->updatePromotedUserCount($input->superiorId);
            }

            // 创建用户余额
            AccountService::getInstance()->createUserAccount($user->id);

            return Auth::guard('user')->login($user);
        });

        return $this->success($token);
    }

    public function wxMpLogin()
    {
        $code = $this->verifyRequiredString('code');
        $result = WxMpServe::new()->getUserOpenid($code);
        $user = UserService::getInstance()->getByOpenid($result['openid']);
        $token = '';
        if (!is_null($user)) {
            $token = Auth::guard('user')->login($user);
        }
        return $this->success($token);
    }

    public function refreshToken() {
        try {
            $token = Auth::guard('user')->refresh();

            // 由于删除用户之后，鉴权失败，但刷新token依旧有效，暂未找到解决办法，因此增加这一层校验
            try {
                Auth::guard('user')->userOrFail();
            } catch (\Exception $e) {
                throw new BusinessException(CodeResponse::FORBIDDEN, 'token失效，请重新登录');
            }
        } catch (\Exception $e) {
            throw new BusinessException(CodeResponse::FORBIDDEN, 'token失效，请重新登录');
        }
        return $this->success($token);
    }
}
