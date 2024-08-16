<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Relation;
use App\Models\User;
use App\Models\UserLevel;
use App\Services\RelationService;
use App\Services\UserLevelService;
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
        $page = UserService::getInstance()->getUserPage($input);
        $userList = collect($page->items());

        $userIds = $userList->pluck('id')->toArray();
        $userLevelList = UserLevelService::getInstance()->getListByUserIds($userIds)->keyBy('user_id');
        $superiorIds = RelationService::getInstance()->getRelationListByFanIds($userIds)->keyBy('fan_id');

        $list = $userList->map(function (User $user) use ($superiorIds, $userLevelList) {
            /** @var UserLevel $userLevel */
            $userLevel = $userLevelList->get($user->id);
            $user['level'] = $userLevel->level;

            /** @var Relation $relation */
            $relation = $superiorIds->get($user->id);
            $user['superiorId'] = $relation->superior_id;

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

    public function teamLeaderOptions()
    {
        $teamLeaderIds = UserLevelService::getInstance()->getOptionsByLevelList([1, 2, 3, 4, 5])->pluck('user_id')->toArray();
        $teamLeaderOptions = UserService::getInstance()->getListByIds($teamLeaderIds, ['id', 'nickname']);
        return $this->success($teamLeaderOptions);
    }
}
