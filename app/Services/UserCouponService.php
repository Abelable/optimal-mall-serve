<?php

namespace App\Services;

use App\Models\UserCoupon;
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

    public function getReceiveCount($userId)
    {
        return UserCoupon::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 1)
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

    public function getUserCouponByCouponId($couponId, $columns = ['*'])
    {
        return UserCoupon::query()->where('coupon_id', $couponId)->first($columns);
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
}
