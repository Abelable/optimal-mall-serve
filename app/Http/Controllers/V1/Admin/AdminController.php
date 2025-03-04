<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\AdminAddInput;
use App\Utils\Inputs\Admin\AdminEditInput;
use App\Utils\Inputs\Admin\AdminListInput;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var AdminListInput $input */
        $input = AdminListInput::new();
        $list = AdminService::getInstance()->getAdminList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $admin = AdminService::getInstance()->getAdminById($id);
        if (is_null($admin)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前管理员不存在');
        }
        return $this->success($admin);
    }

    public function add()
    {
        /** @var AdminAddInput $input */
        $input = AdminAddInput::new();

        $admin = AdminService::getInstance()->getAdminByAccount($input->account);
        if (!is_null($admin)) {
            return $this->fail(CodeResponse::REGISTERED_ACCOUNT);
        }

        $admin = Admin::new();
        if (!empty($input->avatar)) {
            $admin->avatar = $input->avatar;
        }
        if (!empty($input->nickname)) {
            $admin->nickname = $input->nickname;
        }
        $admin->account = $input->account;
        $admin->password = Hash::make($input->password);
        $admin->role_id = $input->roleId;
        $admin->save();

        return $this->success();
    }

    public function edit()
    {
        /** @var AdminEditInput $input */
        $input = AdminEditInput::new();

        $admin = AdminService::getInstance()->getAdminById($input->id);
        if (is_null($admin)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前管理员不存在');
        }

        if (!empty($input->avatar)) {
            $admin->avatar = $input->avatar;
        }
        if (!empty($input->nickname)) {
            $admin->nickname = $input->nickname;
        }
        if (!empty($input->password)) {
            $admin->password = Hash::make($input->password);
        }
        $admin->role_id = $input->roleId;
        $admin->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $admin = AdminService::getInstance()->getAdminById($id);
        if (is_null($admin)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前管理员不存在');
        }
        $admin->delete();
        return $this->success();
    }
}
