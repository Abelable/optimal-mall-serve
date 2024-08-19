<?php

namespace App\Http\Controllers\V1\Admin;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdminService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $guard = 'Admin';
    protected $only = ['info'];

    public function login()
    {
        $account = $this->verifyRequiredString('account');
        $password = $this->verifyRequiredString('password');

        $admin = AdminService::getInstance()->getAdminByAccount($account);
        if (is_null($admin)) {
            return $this->fail(CodeResponse::INVALID_ACCOUNT);
        }

        $isPass = Hash::check($password, $admin->getAuthPassword());
        if (!$isPass) {
            return $this->fail(CodeResponse::INVALID_ACCOUNT);
        }

        $token = Auth::guard('Admin')->login($admin);
        return $this->success($token);
    }

    public function logout()
    {
        Auth::guard('Admin')->logout();
        return $this->success();
    }

    public function refreshToken() {
        try {
            $token = Auth::guard('Admin')->refresh();

            // 由于删除用户之后，鉴权失败，但刷新token依旧有效，暂未找到解决办法，因此增加这一层校验
            try {
                Auth::guard('Admin')->userOrFail();
            } catch (\Exception $e) {
                throw new BusinessException(CodeResponse::FORBIDDEN, 'token失效，请重新登录');
            }
        } catch (\Exception $e) {
            throw new BusinessException(CodeResponse::FORBIDDEN, 'token失效，请重新登录');
        }
        return $this->success($token);
    }

    public function info()
    {
        $admin = $this->admin();
        return $this->success([
            'nickname' => $admin->nickname,
            'avatar' => $admin->avatar
        ]);
    }
}
