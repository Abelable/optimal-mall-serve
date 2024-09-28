<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderGoods;
use App\Models\RefundApplication;
use App\Services\CouponService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\RefundApplicationService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\RefundApplicationInput;
use Illuminate\Support\Facades\DB;

class RefundApplicationController extends Controller
{
    public function refundAmount()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $goodsId = $this->verifyRequiredId('goodsId');
        $couponId = $this->verifyId('couponId');
        $refundAmount = $this->calcRefundAmount($orderId, $goodsId, $couponId);
        return $this->success([
            'amount' => $refundAmount
        ]);
    }

    public function detail()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $goodsId = $this->verifyRequiredId('goodsId');
        $columns = ['status', 'failure_reason',  'refund_amount', 'refund_type', 'refund_reason', 'image_list'];
        $refundApplication = RefundApplicationService::getInstance()->getRefundApplicationByUserId($this->userId(), $orderId, $goodsId, $columns);
        $refundApplication->image_list = json_decode($refundApplication->image_list);
        return $this->success($refundApplication);
    }

    public function add()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $goodsId = $this->verifyRequiredId('goodsId');
        $couponId = $this->verifyId('couponId');
        /** @var RefundApplicationInput $input */
        $input = RefundApplicationInput::new();

        DB::transaction(function () use ($input, $couponId, $goodsId, $orderId) {
            $refundAmount = $this->calcRefundAmount($orderId, $goodsId, $couponId);
            RefundApplicationService::getInstance()->createRefundApplication($this->userId(), $orderId, $goodsId, $couponId, $refundAmount, $input);

            OrderService::getInstance()->afterSale($this->userId(), $orderId);
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var RefundApplicationInput $input */
        $input = RefundApplicationInput::new();

        /** @var RefundApplication $refundApplication */
        $refundApplication = RefundApplicationService::getInstance()->getRefundApplicationById($id);
        if (is_null($refundApplication)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        RefundApplicationService::getInstance()->updateRefundApplication($refundApplication, $input);

        return $this->success();
    }

    public function submitShippingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $shipCode = $this->verifyRequiredString('shipCode');
        $shipSn = $this->verifyRequiredString('shipSn');

        $refundApplication = RefundApplicationService::getInstance()->getUserRefundApplication($this->userId(), $id);
        if (is_null($refundApplication)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        if ($refundApplication->status != 2) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '后台未审核通过，无法上传物流信息');
        }
        $refundApplication->ship_code = $shipCode;
        $refundApplication->ship_sn = $shipSn;
        $refundApplication->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $refundApplication = RefundApplicationService::getInstance()->getUserRefundApplication($this->userId(), $id);
        if (is_null($refundApplication)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        $refundApplication->delete();
        return $this->success();
    }

    private function calcRefundAmount($orderId, $goodsId, $couponId)
    {
        /** @var OrderGoods $orderGoods */
        $orderGoods = OrderGoodsService::getInstance()->getOrderGoods($orderId, $goodsId);
        $totalPrice = bcmul($orderGoods->price, $orderGoods->number, 2);

        $couponDenomination = 0;
        if ($couponId != 0) {
            $coupon = CouponService::getInstance()->getGoodsCoupon($couponId, $goodsId);
            if (!is_null($coupon)) {
                $couponDenomination = $coupon->denomination;
            }
        }

        return bcsub($totalPrice, $couponDenomination, 2);
    }
}
