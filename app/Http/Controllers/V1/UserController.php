<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\UserInfoInput;
use App\Utils\TimServe;

class UserController extends Controller
{
    public function userInfo()
    {
        $user = $this->user();
        $user['promoterId'] = $user->promoterInfo->id ?? 0;
        $user['level'] = $user->promoterInfo->level ?? 0;
        $user['superiorId'] = $user->superiorId();
        $user['authInfoId'] = $user->authInfo->id ?? 0;
        unset($user->openid);
        unset($user->created_at);
        unset($user->updated_at);
        return $this->success($user);
    }

    public function updateUserInfo()
    {
        /** @var UserInfoInput $input */
        $input = UserInfoInput::new();
        $user = $this->user();

        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        if (!empty($input->wxQrcode)) {
            $user->wx_qrcode = $input->wxQrcode;
        }
        if (!empty($input->signature)) {
            $user->signature = $input->signature;
        }

        $user->save();

        return $this->success();
    }

    public function timLoginInfo()
    {
        $timServe = TimServe::new();
        $timServe->updateUserInfo($this->userId(), $this->user()->nickname, $this->user()->avatar);
        $loginInfo = $timServe->getLoginInfo($this->userId());
        return $this->success($loginInfo);
    }

    public function superiorInfo()
    {
        $superiorId = $this->verifyRequiredId('superiorId');

        $superiorInfo = UserService::getInstance()->getUserById($superiorId, ['id', 'mobile', 'avatar', 'nickname', 'gender', 'wx_qrcode', 'signature']);
        if (is_null($superiorInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前上级用户不存在');
        }

        return $this->success($superiorInfo);
    }

    public function customerData()
    {
        $todayNewCustomerCount = RelationService::getInstance()->getTodayCountBySuperiorId($this->userId());

        $customerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $todayOrderingCustomerCount = OrderService::getInstance()->getTodayOrderCountByUserIds($customerIds);

        $customerTotalCount = RelationService::getInstance()->getCountBySuperiorId($this->userId());

        return $this->success([
            'todayNewCount' => $todayNewCustomerCount,
            'todayOrderingCount' => $todayOrderingCustomerCount,
            'totalCount' => $customerTotalCount
        ]);
    }

    public function promoterData()
    {
        $todayCustomerIds = RelationService::getInstance()->getTodayListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $todayNewPromoterCount = PromoterService::getInstance()->getPromoterCountByUserIds($todayCustomerIds);

        $totalCustomerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $totalPromoterIds = PromoterService::getInstance()->getPromoterListByUserIds($totalCustomerIds)->pluck('user_id')->toArray();
        $todayOrderingPromoterCount = OrderService::getInstance()->getTodayOrderCountByUserIds($totalPromoterIds);

        $totalPromoterCount = PromoterService::getInstance()->getPromoterCountByUserIds($totalCustomerIds);

        return $this->success([
            'todayNewCount' => $todayNewPromoterCount,
            'todayOrderingCount' => $todayOrderingPromoterCount,
            'totalCount' => $totalPromoterCount
        ]);
    }
}
