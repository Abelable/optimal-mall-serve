<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CouponExpire;
use App\Models\Coupon;
use App\Services\GoodsService;
use App\Services\CouponService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CouponEditInput;
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
        $columns = ['id', 'denomination', 'name', 'description', 'type', 'num_limit', 'price_limit', 'expiration_time'];
        $coupon = CouponService::getInstance()->getCouponById($id, $columns);
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
            $coupon->type = $input->type;
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

            if (!is_null($input->expirationTime)) {
                $this->dispatch(new CouponExpire($coupon->id, $input->expirationTime));
            }
        }

        return $this->success();
    }

    public function edit()
    {
        /** @var CouponEditInput $input */
        $input = CouponEditInput::new();

        $coupon = CouponService::getInstance()->getCouponById($input->id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        $coupon->denomination = $input->denomination;
        $coupon->name = $input->name;
        $coupon->description = $input->description;
        $coupon->type = $input->type;
        if (!is_null($input->numLimit)) {
            $coupon->num_limit = $input->numLimit;
        }
        if (!is_null($input->priceLimit)) {
            $coupon->price_limit = $input->priceLimit;
        }
        if (!is_null($input->expirationTime)) {
            $coupon->expiration_time = $input->expirationTime;
            $this->dispatch(new CouponExpire($coupon->id, $input->expirationTime));
        }
        $coupon->save();

        return $this->success();
    }

    public function editReceivedNum()
    {
        $id = $this->verifyRequiredId('id');
        $num = $this->verifyRequiredInteger('num');

        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        $coupon->received_num = $num;
        $coupon->save();

        return $this->success();
    }


    public function down()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        $coupon->status = 3;
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
