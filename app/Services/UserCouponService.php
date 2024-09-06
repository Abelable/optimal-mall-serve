<?php

namespace App\Services;

use App\Models\UserCoupon;
use App\Utils\Inputs\StatusPageInput;

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
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get($columns);
    }

    public function getListByCouponIds($userId, array $couponIds, $columns = ['*'])
    {
        return UserCoupon::query()
            ->where('user_id', $userId)
            ->where('status', 1)
            ->whereIn('coupon_id', $couponIds)
            ->get($columns);
    }
}
