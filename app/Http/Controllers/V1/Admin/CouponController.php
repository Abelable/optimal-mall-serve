<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\GoodsService;
use App\Services\CouponService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CouponPageInput;
use App\Utils\Inputs\Admin\CouponInput;

class CouponController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var CouponPageInput $input */
        $input = CouponPageInput::new();
        $list = CouponService::getInstance()->getCouponPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id, ['id', 'status', 'name', 'start_time', 'end_time', 'goods_type']);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        return $this->success($coupon);
    }

    public function add()
    {
        /** @var CouponInput $input */
        $input = CouponInput::new();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $coupon = Coupon::new();
            $coupon->denomination = $input->denomination;
            $coupon->name = $input->name;
            $coupon->description = $input->description;
            $coupon->goods_id = $goods->id;
            $coupon->goods_cover = $goods->cover;
            $coupon->goods_name = $goods->name;
            if (!is_null($input->numLimit)) {
                $coupon->num_limit = $input->numLimit;
            }
            if (!is_null($input->priceLimit)) {
                $coupon->price_limit = $input->priceLimit;
            }
            if (!is_null($input->expirationTime)) {
                $coupon->expiration_time = $input->expirationTime;
            }
            $coupon->save();
        }

        return $this->success();
    }

    public function edit()
    {
        /** @var CouponInput $input */
        $input = CouponInput::new();
        $id = $this->verifyRequiredInteger('id');

        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        $coupon->name = $input->name;
        $coupon->status = $input->status;
        if (!is_null($input->startTime)) {
            $coupon->start_time = $input->startTime;
        }
        if (!is_null($input->endTime)) {
            $coupon->end_time = $input->endTime;
        }
        $coupon->goods_type = $input->goodsType;
        $coupon->save();

        return $this->success();
    }

    public function editFollowers()
    {
        $id = $this->verifyRequiredInteger('id');
        $followers = $this->verifyRequiredInteger('followers');

        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        $coupon->followers = $followers;
        $coupon->save();

        return $this->success();
    }

    public function editSales()
    {
        $id = $this->verifyRequiredInteger('id');
        $sales = $this->verifyRequiredInteger('sales');

        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        $coupon->sales = $sales;
        $coupon->save();

        return $this->success();
    }


    public function end()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        $coupon->status = 2;
        $coupon->save();
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        $coupon->delete();
        return $this->success();
    }
}
