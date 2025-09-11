<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Promoter;
use App\Services\PromoterService;
use App\Services\UserService;
use App\Utils\Inputs\Admin\UserPageInput;

class PromoterController extends Controller
{
    protected $only = [];

    public function list()
    {
        /** @var UserPageInput $input */
        $input = UserPageInput::new();
        $page = PromoterService::getInstance()->getPromoterPage($input);
        $promoterList = collect($page->items());

        $userIds = $promoterList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $promoterList->map(function (Promoter $promoter) use ($userList) {
            return $userList->get($promoter->user_id);
        });

        return $this->success($this->paginate($page, $list));
    }
}
