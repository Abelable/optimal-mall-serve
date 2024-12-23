<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\GoodsService;
use App\Services\CouponService;
use App\Services\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CouponPageInput;
use App\Utils\Inputs\Admin\CouponInput;
use Illuminate\Support\Facades\DB;

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
            CouponService::getInstance()->updateCoupon($coupon, $input, $goods);
        }

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var CouponInput $input */
        $input = CouponInput::new();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name'])->keyBy('id');
        foreach ($input->goodsIds as $goodsId) {
            $coupon = CouponService::getInstance()->getGoodsCoupon($id, $goodsId);
            if (is_null($coupon)) {
                $coupon = Coupon::new();
            }
            CouponService::getInstance()->updateCoupon($coupon, $input, $goodsList->get($goodsId));
        }

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

    public function up()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        $coupon->status = 1;
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

        DB::transaction(function () use ($coupon) {
            $coupon->delete();
            UserCouponService::getInstance()->deleteByCouponId($coupon->id);
        });

        return $this->success();
    }
}
