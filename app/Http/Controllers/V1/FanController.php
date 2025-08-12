<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FanService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class FanController extends Controller
{
    public function follow()
    {
        $authorId = $this->verifyRequiredId('authorId');
        if ($authorId == $this->userId()) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '不能关注自己哦');
        }

        FanService::getInstance()->newFan($authorId, $this->userId());
        return $this->success();
    }

    public function cancelFollow()
    {
        $authorId = $this->verifyRequiredId('authorId');

        $fan = FanService::getInstance()->fan($authorId, $this->userId());
        if (is_null($fan)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您未关注该主播');
        }

        $fan->delete();

        return $this->success();
    }

    public function followStatus()
    {
        $authorId = $this->verifyRequiredId('authorId');

        if ($authorId == $this->userId()) {
            $isFollow = true;
        } else {
            $fanIds = FanService::getInstance()->fanIds($authorId);
            $isFollow = in_array($this->userId(), $fanIds);
        }

        return $this->success([
            'isFollow' => $isFollow
        ]);
    }

    public function followList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = FanService::getInstance()->followPaginate($this->userId(), $input);

        $authorIds = collect($page->items())->pluck('author_id')->toArray();

        // 被关注用户的关注列表
        $authorIdsGroup = FanService::getInstance()->authorIdsGroup($authorIds);

        $followList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname', 'signature']);

        $list = $followList->map(function (User $user) use ($authorIdsGroup) {
            $authorIds = $authorIdsGroup->get($user->id) ?: [];

            if (in_array($this->userId(), $authorIds)) {
                $user['status'] = 2;
            } else {
                $user['status'] = 1;
            }

            return $user;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function fanList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = FanService::getInstance()->fanPaginate($this->userId(), $input);

        $fanIds = collect($page->items())->pluck('fan_id')->toArray();

        // 粉丝用户的粉丝列表
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($fanIds);

        $fanList = UserService::getInstance()->getListByIds($fanIds, ['id', 'avatar', 'nickname', 'signature']);

        $list = $fanList->map(function (User $user) use ($fanIdsGroup) {
            $fanIds = $fanIdsGroup->get($user->id) ?: [];

            if (in_array($this->userId(), $fanIds)) {
                $user['status'] = 2;
            } else {
                $user['status'] = 1;
            }

            return $user;
        });

        return $this->success($this->paginate($page, $list));
    }
}
