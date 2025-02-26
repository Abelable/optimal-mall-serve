<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\MerchantService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\RefundService;
use App\Utils\CodeResponse;
use App\Utils\ExpressServe;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $columns = ['id', 'user_id', 'status', 'failure_reason', 'order_sn', 'refund_type', 'refund_amount', 'created_at', 'updated_at'];
        $page = RefundService::getInstance()->getRefundList($input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }
        $refund->image_list = json_decode($refund->image_list);
        $goods = OrderGoodsService::getInstance()->getOrderGoods($refund->order_id, $refund->goods_id);
        $refund['goodsInfo'] = $goods;
        return $this->success($refund);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }

        if (($refund->status == 0 && $refund->refund_type == 1)
            || ($refund->status == 2 && $refund->refund_type == 2)) {
            DB::transaction(function () use ($refund) {
                $refund->status = 3;
                $refund->save();

                OrderService::getInstance()->afterSaleRefund(
                    $refund->order_id,
                    $refund->goods_id,
                    $refund->coupon_id,
                    $refund->refund_amount
                );
            });
        } else {
            $refund->status = 1;
            $refund->save();
        }

        return $this->success();
    }

    public function shippingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }
        $goods = OrderGoodsService::getInstance()->getOrderGoods($refund->order_id, $refund->goods_id);
        $merchant = MerchantService::getInstance()->getMerchantById($goods->merchant_id);
        $traces = ExpressServe::new()->track($refund->ship_code, $refund->ship_sn, $merchant->mobile);
        return $this->success([
            'shipCode' => $refund->ship_code,
            'shipSn' => $refund->ship_sn,
            'traces' => $traces
        ]);
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }

        $refund->status = 4;
        $refund->failure_reason = $reason;
        $refund->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }
        $refund->delete();
        return $this->success();
    }

    public function waitingRefundCount()
    {
        $count = RefundService::getInstance()->getCountByStatus(0);
        return $this->success($count);
    }
}
