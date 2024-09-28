<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\RefundService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $columns = ['id', 'user_id', 'status', 'failure_reason', 'order_sn', 'created_at', 'updated_at'];
        $page = RefundService::getInstance()->getRefundList($input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
        }
        return $this->success($refund);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
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

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
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
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
        }
        $refund->delete();
        return $this->success();
    }
}
