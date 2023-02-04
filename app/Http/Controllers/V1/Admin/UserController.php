<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\UserListInput;

class UserController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $input = UserListInput::new();
        $list = UserService::getInstance()->getUserList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $user = UserService::getInstance()->getUserById($id);
        if (is_null($user)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前用户不存在');
        }
        return $this->success($user);
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $user = UserService::getInstance()->getUserById($id);
        if (is_null($user)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前用户不存在');
        }
        $user->delete();
        return $this->success();
    }
}
