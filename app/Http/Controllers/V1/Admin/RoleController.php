<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Services\Admin\RoleService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class RoleController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = RoleService::getInstance()->getRoleList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $role = RoleService::getInstance()->getRoleById($id);
        if (is_null($role)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前管理员角色不存在');
        }
        return $this->success($role);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');
        $desc = $this->verifyString('desc');
        $permission = $this->verifyRequiredString('permission');

        $role = RoleService::getInstance()->getRoleByName($name);
        if (!is_null($role)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '管理员角色已存在');
        }

        $role = AdminRole::new();
        $role->name = $name;
        $role->desc = $desc;
        $role->permission = $permission;
        $role->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $name = $this->verifyRequiredString('name');
        $desc = $this->verifyString('desc');
        $permission = $this->verifyRequiredString('permission');

        $role = RoleService::getInstance()->getRoleById($id);
        if (is_null($role)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前管理员角色不存在');
        }

        $role->name = $name;
        $role->desc = $desc;
        $role->permission = $permission;
        $role->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $role = RoleService::getInstance()->getRoleById($id);
        if (is_null($role)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前管理员角色不存在');
        }
        $role->delete();
        return $this->success();
    }

    public function options()
    {
        $options = RoleService::getInstance()->getRoleOptions(['id', 'name']);
        return $this->success($options);
    }
}
