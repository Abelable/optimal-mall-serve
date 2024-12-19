<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promoter;
use App\Models\User;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\UserPageInput;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;

class PromoterController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var UserPageInput $input */
        $input = UserPageInput::new();

        if (!empty($input->nickname) || !empty($input->mobile)) {
            $page = UserService::getInstance()->getUserPage($input);
            $userList = collect($page->items());
            $userIds = $userList->pluck('id')->toArray();
            $promoterList = PromoterService::getInstance()->getPromoterListByUserIds($userIds)->keyBy('user_id');
            $list = $userList->map(function (User $user) use ($promoterList) {
                $promoter = $promoterList->get($user->id);
                if (!is_null($promoter)) {
                    $promoter['avatar'] = $user->avatar;
                    $promoter['nickname'] = $user->nickname;
                    $promoter['mobile'] = $user->mobile;
                }
                return $promoter;
            })->filter(function ($promoter) {
                return !is_null($promoter);
            })->values();
        } else {
            $page = PromoterService::getInstance()->getPromoterPage($input);
            $promoterList = collect($page->items());

            $userIds = $promoterList->pluck('user_id')->toArray();
            $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname', 'mobile'])->keyBy('id');

            $list = $promoterList->map(function (Promoter $promoter) use ($userList) {
                /** @var User $user */
                $user = $userList->get($promoter->user_id);
                $promoter['avatar'] = $user->avatar;
                $promoter['nickname'] = $user->nickname;
                $promoter['mobile'] = $user->mobile;
                return $promoter;
            });
        }

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $promoter = PromoterService::getInstance()->getPromoterById($id);
        if (is_null($promoter)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前推广员不存在');
        }
        return $this->success($promoter);
    }

    public function add()
    {
        $userId = $this->verifyRequiredId('userId');
        $level = $this->verifyRequiredInteger('level');
        $scene = $this->verifyRequiredInteger('scene');
        PromoterService::getInstance()->create($userId, $level, $scene);
        return $this->success();
    }

    public function changeLevel()
    {
        $id = $this->verifyRequiredId('id');
        $level = $this->verifyRequiredInteger('level');
        $scene = $this->verifyRequiredInteger('scene');

        $promoter = PromoterService::getInstance()->getPromoterById($id);
        if (is_null($promoter)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前推广员不存在');
        }
        $promoter->level = $level;
        $promoter->scene = $scene;
        $promoter->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $promoter = PromoterService::getInstance()->getPromoterById($id);
        if (is_null($promoter)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前推广员不存在');
        }
        DB::transaction(function () use ($promoter) {
            $promoter->delete();

            // 删除上下级关系
            RelationService::getInstance()->deleteBySuperiorId($promoter->user_id);
        });

        return $this->success();
    }

    public function options()
    {
        $userIds = PromoterService::getInstance()->getOptions()->pluck('user_id')->toArray();
        $options = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname']);
        return $this->success($options);
    }
}
