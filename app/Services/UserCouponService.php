<?php

namespace App\Services;

use App\Models\UserCoupon;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class UserCouponService extends BaseService
{
    public function getUserCouponPage($userId, StatusPageInput $input, $columns = ['*'])
    {
        return UserCoupon::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserCouponList($userId, $columns = ['*'])
    {
        return UserCoupon::query()
            ->where('status', 1)
            ->where('user_id', $userId)
            ->get($columns);
    }

    public function getUsedCount($userId)
    {
        return UserCoupon::query()
            ->where('user_id', $userId)
            ->where('status', 2)
            ->where('created_at', '>=', now()->subDays(7))
            ->select('coupon_id', DB::raw('count(*) as receive_count'))
            ->groupBy('coupon_id')
            ->get();
    }

    public function getListByCouponIds($userId, array $couponIds, $columns = ['*'])
    {
        return UserCoupon::query()
            ->where('status', 1)
            ->where('user_id', $userId)
            ->whereIn('coupon_id', $couponIds)
            ->get($columns);
    }

    public function getUserCoupon($userId, $couponId, $columns = ['*'])
    {
        return UserCoupon::query()
            ->where('status', 1)
            ->where('user_id', $userId)
            ->where('coupon_id', $couponId)
            ->first($columns);
    }

    public function getUserUsedCouponByCouponId($userId, $couponId, $columns = ['*'])
    {
        return UserCoupon::query()
            ->where('status', 2)
            ->where('user_id', $userId)
            ->where('coupon_id', $couponId)
            ->first($columns);
    }

    public function useCoupon($userId, $couponId)
    {
        $userCoupon = $this->getUserCoupon($userId, $couponId);
        $userCoupon->status = 2;
        $userCoupon->save();
        return $userCoupon;
    }

    public function deleteByCouponId($couponId)
    {
        return UserCoupon::query()->where('coupon_id', $couponId)->delete();
    }

    public function expireCoupon($couponId)
    {
        $couponList = $this->getListByCouponId($couponId);
        foreach ($couponList as $coupon) {
            if (is_null($coupon)) {
                $this->throwBusinessException(CodeResponse::NOT_FOUND, '用户领取的优惠券不存在');
            }
            $coupon->status = 3;
            $coupon->save();
        }
    }

    public function getListByCouponId($couponId, $columns = ['*'])
    {
        return UserCoupon::query()->where('coupon_id', $couponId)->get($columns);
    }
}
