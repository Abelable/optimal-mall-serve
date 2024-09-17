<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Relation;
use App\Models\User;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\UserPageInput;

class UserController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var UserPageInput $input */
        $input = UserPageInput::new();

        $userIds = null;
        if (!empty($input->superiorId)) {
            $userIds = RelationService::getInstance()->getRelationListBySuperiorIds([$input->superiorId])->pluck('fan_id')->toArray();
        }

        $page = UserService::getInstance()->getUserPage($input, $userIds);
        $userList = collect($page->items());

        $userIds = $userList->pluck('id')->toArray();
        $relationList = RelationService::getInstance()->getListByFanIds($userIds)->keyBy('fan_id');

        $list = $userList->map(function (User $user) use ($relationList) {
            /** @var Relation $relation */
            $relation = $relationList->get($user->id);
            $user['superiorId'] = $relation ? $relation->superior_id : 0;

            return $user;
        });
        return $this->success($this->paginate($page, $list));
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
