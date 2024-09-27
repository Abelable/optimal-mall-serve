<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\RefundApplicationService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class RefundApplicationController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $columns = ['id', 'user_id', 'status', 'failure_reason', 'order_sn', 'created_at', 'updated_at'];
        $page = RefundApplicationService::getInstance()->getRefundApplicationList($input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $refundApplication = RefundApplicationService::getInstance()->getRefundApplicationById($id);
        if (is_null($refundApplication)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
        }
        return $this->success($refundApplication);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $refundApplication = RefundApplicationService::getInstance()->getRefundApplicationById($id);
        if (is_null($refundApplication)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
        }

        if (($refundApplication->status == 0 && $refundApplication->refund_type === 1)
            || ($refundApplication->status == 2 && $refundApplication->refund_type === 2)) {
            DB::transaction(function () use ($refundApplication) {
                $refundApplication->status = 3;
                $refundApplication->save();

                OrderService::getInstance()->afterSaleRefund(
                    $refundApplication->order_id,
                    $refundApplication->goods_id,
                    $refundApplication->coupon_id,
                    $refundApplication->refund_amount
                );
            });
        } else {
            $refundApplication->status = 1;
            $refundApplication->save();
        }

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $refundApplication = RefundApplicationService::getInstance()->getRefundApplicationById($id);
        if (is_null($refundApplication)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
        }

        $refundApplication->status = 4;
        $refundApplication->failure_reason = $reason;
        $refundApplication->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $refundApplication = RefundApplicationService::getInstance()->getRefundApplicationById($id);
        if (is_null($refundApplication)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前售后信息不存在');
        }
        $refundApplication->delete();
        return $this->success();
    }
}
