<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderGoods;
use App\Models\Refund;
use App\Services\AdminTodoService;
use App\Services\CouponService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\RefundService;
use App\Utils\CodeResponse;
use App\Utils\Enums\NotificationEnums;
use App\Utils\Inputs\RefundInput;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function refundAmount()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $goodsId = $this->verifyRequiredId('goodsId');
        $couponId = $this->verifyId('couponId');
        $refundAmount = $this->calcRefundAmount($orderId, $goodsId, $couponId);
        return $this->success($refundAmount);
    }

    public function detail()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $goodsId = $this->verifyRequiredId('goodsId');
        $columns = ['id', 'status', 'failure_reason',  'refund_amount', 'refund_type', 'refund_reason', 'image_list', 'ship_code', 'ship_sn'];
        $refund = RefundService::getInstance()->getRefundByUserId($this->userId(), $orderId, $goodsId, $columns);
        if (!is_null($refund)) {
            $refund->image_list = json_decode($refund->image_list);
        }
        return $this->success($refund);
    }

    public function add()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $orderSn = $this->verifyRequiredString('orderSn');
        $goodsId = $this->verifyRequiredId('goodsId');
        $couponId = $this->verifyId('couponId');
        /** @var RefundInput $input */
        $input = RefundInput::new();

        DB::transaction(function () use ($orderSn, $input, $couponId, $goodsId, $orderId) {
            $refundAmount = $this->calcRefundAmount($orderId, $goodsId, $couponId);
            $refund = RefundService::getInstance()->createRefund($this->userId(), $orderId, $orderSn, $goodsId, $couponId, $refundAmount, $input);

            OrderService::getInstance()->afterSale($this->userId(), $orderId);

            // 生成后台售后处理代办事项
            AdminTodoService::getInstance()->createTodo(NotificationEnums::REFUND_NOTICE, [$refund->id]);
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var RefundInput $input */
        $input = RefundInput::new();

        /** @var Refund $refund */
        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        RefundService::getInstance()->updateRefund($refund, $input);

        return $this->success();
    }

    public function submitShippingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $shipCode = $this->verifyRequiredString('shipCode');
        $shipSn = $this->verifyRequiredString('shipSn');

        $refund = RefundService::getInstance()->getUserRefund($this->userId(), $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        if ($refund->status != 1) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '后台未审核通过，无法上传物流信息');
        }
        $refund->status = 2;
        $refund->ship_code = $shipCode;
        $refund->ship_sn = $shipSn;
        $refund->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $refund = RefundService::getInstance()->getUserRefund($this->userId(), $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        $refund->delete();
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
