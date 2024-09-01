<?php

namespace App\Services;

use App\Models\Coupon;
use App\Utils\Inputs\CouponPageInput;

class CouponService extends BaseService
{
    public function getCouponPage(CouponPageInput $input, $columns = ['*'])
    {
        $query = Coupon::query();
        if (!is_null($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->goodsId)) {
            $query = $query->where('goods_id', $input->goodsId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCouponList($status, $columns = ['*'])
    {
        return Coupon::query()->where('status', $status)->get($columns);
    }

    public function getCouponListByGoodsIds(array $goodsIds, $columns = ['*'])
    {
        return Coupon::query()->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getCouponById($id, $columns = ['*'])
    {
        return Coupon::query()->find($id, $columns);
    }
}