<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Services\CouponService;
use App\Services\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    protected $except = ['goodsCouponList'];

    public function goodsCouponList()
    {
        $goodsId = $this->verifyRequiredInteger('goodsId');
        $couponList = CouponService::getInstance()->getCouponListByGoodsId($goodsId);

        $userCouponList = [];
        if ($this->isLogin()) {
            $couponIds = $couponList->pluck('id')->toArray();
            $userCouponList = UserCouponService::getInstance()->getListByCouponIds($this->userId(), $couponIds)->keyBy('coupon_id');
        }

        $list = $couponList->map(function (Coupon $coupon) use ($userCouponList) {
            $userCoupon = count($userCouponList) != 0 ? $userCouponList->get($coupon->id) : null;
            $coupon['isReceived'] = !is_null($userCoupon) ? 1 : 0;
            return $coupon;
        });

        return $this->success($list);
    }

    public function receiveCoupon()
    {
        $id = $this->verifyRequiredInteger('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        DB::transaction(function () use ($coupon) {
            $userCoupon = UserCoupon::new();
            $userCoupon->user_id = $this->userId();
            $userCoupon->coupon_id = $coupon->id;
            $userCoupon->save();

            $coupon->received_num = $coupon->received_num + 1;
            $coupon->save();
        });

        return $this->success();
    }

    public function userCouponList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = UserCouponService::getInstance()->getUserCouponPage($this->userId(), $input);
        $userCouponList = collect($page->items());

        $couponIds = $userCouponList->pluck('coupon_id')->toArray();
        $couponList = CouponService::getInstance()->getCouponListByIds($couponIds)->keyBy('id');

        $list = $userCouponList->map(function (UserCoupon $userCoupon) use ($couponList) {
            /** @var Coupon $coupon */
            $coupon = $couponList->get($userCoupon->coupon_id);
            $coupon->status = $userCoupon->status;
            return $coupon;
        });

        return $this->success($this->paginate($page, $list));
    }
}
