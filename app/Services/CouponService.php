<?php

namespace App\Services;

use App\Models\Coupon;
use App\Utils\CodeResponse;
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
        if (!is_null($input->type)) {
            $query = $query->where('type', $input->type);
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
        return Coupon::query()->where('status', 1)->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getCouponListByGoodsId($goodsId, $columns = ['*'])
    {
        return Coupon::query()->where('status', 1)->where('goods_id', $goodsId)->get($columns);
    }

    public function getCouponById($id, $columns = ['*'])
    {
        return Coupon::query()->find($id, $columns);
    }

    public function getGoodsCoupon($id, $goodsId, $columns = ['*'])
    {
        return Coupon::query()->where('goods_id', $goodsId)->find($id, $columns);
    }

    public function getCouponListByIds(array $ids, $columns = ['*'])
    {
        return Coupon::query()->whereIn('id', $ids)->get($columns);
    }

    public function getAvailableCouponById($id, $columns = ['*'])
    {
        return Coupon::query()->where('status', 1)->find($id, $columns);
    }

    public function getAvailableCouponListByIds(array $ids, $columns = ['*'])
    {
        return Coupon::query()->where('status', 1)->whereIn('id', $ids)->get($columns);
    }

    public function expireCoupon($id)
    {
        $coupon = $this->getCouponById($id);
        if (is_null($coupon)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        $coupon->status = 2;
        $coupon->save();
        return $coupon;
    }
}
