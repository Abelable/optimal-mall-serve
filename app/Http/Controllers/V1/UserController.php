<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Promoter;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\OrderService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\UserInfoInput;
use App\Utils\TimServe;

class UserController extends Controller
{
    protected $except = ['superiorInfo'];

    public function userInfo()
    {
        $user = $this->user();
        $user['promoterId'] = $user->promoterInfo->id ?? 0;
        $user['level'] = $user->promoterInfo->level ?? 0;
        $user['superiorId'] = $user->superiorId();
        $user['authInfoId'] = $user->authInfo->id ?? 0;
        $user['enterpriseInfoId'] = $user->enterpriseInfo->id ?? 0;
        unset($user->openid);
        unset($user->promoterInfo);
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
            return $this->fail(CodeResponse::NOT_FOUND, '用户上级不存在');
        }

        return $this->success($superiorInfo);
    }

    public function customerData()
    {
        $todayNewCustomerCount = RelationService::getInstance()->getTodayCountBySuperiorId($this->userId());

        $customerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $todayOrderingCustomerCount = OrderService::getInstance()->getTodayOrderingUserCountByUserIds($customerIds);

        $customerTotalCount = RelationService::getInstance()->getCountBySuperiorId($this->userId());

        return $this->success([
            'todayNewCount' => $todayNewCustomerCount,
            'todayOrderingCount' => $todayOrderingCustomerCount,
            'totalCount' => $customerTotalCount
        ]);
    }

    public function todayNewCustomerList()
    {
        $todayNewCustomerIds = RelationService::getInstance()->getTodayListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $customerList = UserService::getInstance()->getListByIds($todayNewCustomerIds);
        $list = $this->handleCustomerList($todayNewCustomerIds, $customerList);
        return $this->success($list);
    }

    public function todayOrderingCustomerList()
    {
        $totalCustomerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $todayOrderingCustomerIds = OrderService::getInstance()->getTodayOrderListByUserIds($totalCustomerIds)->pluck('user_id')->toArray();
        $customerList = UserService::getInstance()->getListByIds($todayOrderingCustomerIds);
        $list = $this->handleCustomerList($todayOrderingCustomerIds, $customerList);
        return $this->success($list);
    }

    public function customerList()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();

        $totalCustomerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        if (!empty($input->keywords)) {
            $userList = UserService::getInstance()->searchListByUserIds($totalCustomerIds, $input->keywords);
            $totalCustomerIds = $userList->pluck('id')->toArray();
        }
        $page = UserService::getInstance()->getPageByUserIds($totalCustomerIds, $input);
        $customerList = collect($page->items());

        $list = $this->handleCustomerList($totalCustomerIds, $customerList);

        return $this->success($this->paginate($page, $list));
    }

    private function handleCustomerList($customerIds, $customerList)
    {
        $promoterList = PromoterService::getInstance()->getPromoterListByUserIds($customerIds)->keyBy('user_id');

        $userCommissionList = CommissionService::getInstance()->getSettledCommissionListByUserIds($customerIds)->groupBy('user_id');
        $superiorCommissionList = CommissionService::getInstance()->getSettledCommissionListBySuperiorIds($customerIds)->groupBy('superior_id');

        return $customerList->map(function (User $user) use ($promoterList, $userCommissionList, $superiorCommissionList) {
            $promoter = $promoterList->get($user->id);

            $userCommission = $userCommissionList->get($user->id);
            $userCommissionSum = $userCommission ? $userCommission->sum('commission_base') : 0;

            $superiorCommission = $superiorCommissionList->get($user->id);
            $superiorCommissionSum = $superiorCommission ? $superiorCommission->sum('commission_base') : 0;

            return [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'nickname' => $user->nickname,
                'mobile' => $user->mobile,
                'promoterId' => $promoter ? $promoter->id : 0,
                'level' => $promoter ? $promoter->level : 0,
                'GMV' => bcadd($userCommissionSum, $superiorCommissionSum, 2),
                'createdAt' => $user->created_at
            ];
        });
    }

    public function promoterData()
    {
        $todayNewCustomerIds = RelationService::getInstance()->getTodayListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $todayNewPromoterCount = PromoterService::getInstance()->getPromoterCountByUserIds($todayNewCustomerIds);

        $totalCustomerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $totalPromoterIds = PromoterService::getInstance()->getPromoterListByUserIds($totalCustomerIds)->pluck('user_id')->toArray();

        $todayOrderingPromoterCount = OrderService::getInstance()->getTodayOrderingUserCountByUserIds($totalPromoterIds);

        $totalPromoterCount = PromoterService::getInstance()->getPromoterCountByUserIds($totalCustomerIds);

        return $this->success([
            'todayNewCount' => $todayNewPromoterCount,
            'todayOrderingCount' => $todayOrderingPromoterCount,
            'totalCount' => $totalPromoterCount
        ]);
    }

    public function todayNewPromoterList()
    {
        $todayNewCustomerIds = RelationService::getInstance()->getTodayListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $todayNewPromoterList = PromoterService::getInstance()->getPromoterListByUserIds($todayNewCustomerIds);
        $promoterIds = $todayNewPromoterList->pluck('user_id')->toArray();
        $list = $this->handlePromoterList($promoterIds, $todayNewPromoterList);
        return $this->success($list);
    }

    public function todayOrderingPromoterList()
    {
        $totalCustomerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $totalPromoterIds = PromoterService::getInstance()->getPromoterListByUserIds($totalCustomerIds)->pluck('user_id')->toArray();
        $todayOrderingUserIds= OrderService::getInstance()->getTodayOrderListByUserIds($totalPromoterIds)->pluck('user_id')->toArray();
        $todayOrderingPromoterList = PromoterService::getInstance()->getPromoterListByUserIds($todayOrderingUserIds);
        $list = $this->handlePromoterList($todayOrderingUserIds, $todayOrderingPromoterList);
        return $this->success($list);
    }

    public function promoterList()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();

        $totalCustomerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        if (!empty($input->keywords)) {
            $userList = UserService::getInstance()->searchListByUserIds($totalCustomerIds, $input->keywords);
            $totalCustomerIds = $userList->pluck('id')->toArray();
        }
        $page = PromoterService::getInstance()->getPromoterPageByUserIds($totalCustomerIds, $input);
        $promoterList = collect($page->items());

        $promoterIds = $promoterList->pluck('user_id')->toArray();
        $list = $this->handlePromoterList($promoterIds, $promoterList);

        return $this->success($this->paginate($page, $list));
    }

    private function handlePromoterList($promoterIds, $promoterList)
    {
        $userList = UserService::getInstance()->getListByIds($promoterIds)->keyBy('id');

        $userCommissionList = CommissionService::getInstance()->getSettledCommissionListByUserIds($promoterIds)->groupBy('user_id');
        $superiorCommissionList = CommissionService::getInstance()->getSettledCommissionListBySuperiorIds($promoterIds)->groupBy('superior_id');

        return $promoterList->map(function (Promoter $promoter) use ($userList, $userCommissionList, $superiorCommissionList) {
            /** @var User $userInfo */
            $userInfo = $userList->get($promoter->user_id);

            $userCommission = $userCommissionList->get($promoter->user_id);
            $userCommissionSum = $userCommission ? $userCommission->sum('commission_base') : 0;

            $superiorCommission = $superiorCommissionList->get($promoter->user_id);
            $superiorCommissionSum = $superiorCommission ? $superiorCommission->sum('commission_base') : 0;

            return [
                'id' => $userInfo->id,
                'avatar' => $userInfo->avatar,
                'nickname' => $userInfo->nickname,
                'mobile' => $userInfo->mobile,
                'promoterId' => $promoter->id,
                'level' => $promoter->level,
                'GMV' => bcadd($userCommissionSum, $superiorCommissionSum, 2),
                'createdAt' => $promoter->created_at
            ];
        });
    }
}
