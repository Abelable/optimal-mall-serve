<?php

namespace App\Services;

use App\Jobs\CouponExpire;
use App\Models\Coupon;
use App\Models\Goods;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CouponInput;
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
        // todo 优惠券到期处理
        $list = Coupon::query()->where('status', 1)->where('goods_id', $goodsId)->get($columns);
        return $list->map(function (Coupon $coupon) {
            if (strtotime($coupon->expiration_time) <= time()) {
                $coupon->status = 2;
                $coupon->save();
                return null;
            }
            return $coupon;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();
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

    public function updateCoupon(Coupon $coupon, CouponInput $input, Goods $goods)
    {
        if ($coupon->status == 2) {
            $coupon->status = 1;
        }
        $coupon->denomination = $input->denomination;
        $coupon->name = $input->name;
        $coupon->description = $input->description;
        $coupon->goods_id = $goods->id;
        $coupon->goods_cover = $goods->cover;
        $coupon->goods_name = $goods->name;
        $coupon->type = $input->type;
        $coupon->num_limit = $input->numLimit ?? 0;
        $coupon->price_limit = $input->priceLimit ?? 0;
        $coupon->receive_num_limit = $input->receiveNumLimit ?? 0;
        if (!is_null($input->expirationTime)) {
            $coupon->expiration_time = $input->expirationTime;
            dispatch(new CouponExpire($coupon->id, $input->expirationTime));
        }
        $coupon->save();
    }
}
