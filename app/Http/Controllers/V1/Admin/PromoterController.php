<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promoter;
use App\Models\User;
use App\Services\PromoterService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PromoterPageInput;

class PromoterController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PromoterPageInput $input */
        $input = PromoterPageInput::new();

        $page = PromoterService::getInstance()->getPromoterPage($input);
        $promoterList = collect($page->items());

        $userIds = $promoterList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $promoterList->map(function (Promoter $promoter) use ($userList) {
            /** @var User $user */
            $user = $userList->get($promoter->user_id);
            $promoter['avatar'] = $user->avatar;
            $promoter['nickname'] = $user->nickname;
            return $promoter;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        $userId = $this->verifyRequiredId('userId');
        $level = $this->verifyRequiredInteger('level');
        $scene = $this->verifyRequiredInteger('scene');
        PromoterService::getInstance()->create($userId, $level, $scene);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $promoter = PromoterService::getInstance()->getPromoterById($id);
        if (is_null($promoter)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前推广员不存在');
        }
        $promoter->delete();
        return $this->success();
    }

    public function options()
    {
        $userIds = PromoterService::getInstance()->getOptions()->pluck('user_id')->toArray();
        $options = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname']);
        return $this->success($options);
    }
}
