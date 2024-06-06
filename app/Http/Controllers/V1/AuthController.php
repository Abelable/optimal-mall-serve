<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\WxMpRegisterInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\Auth;

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
        $input = WxMpRegisterInput::new();

        $result = WxMpServe::new()->getUserOpenid($input->code);
        $user = UserService::getInstance()->getByOpenid($result['openid']);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_NAME_REGISTERED);
        }

        $user = UserService::getInstance()->register($result['openid'], $input);
        $token = Auth::guard('user')->login($user);
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
