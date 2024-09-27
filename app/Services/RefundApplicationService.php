<?php

namespace App\Services;

use App\Models\RefundApplication;
use App\Utils\Inputs\RefundApplicationInput;
use App\Utils\Inputs\StatusPageInput;

class RefundApplicationService extends BaseService
{
    public function createRefundApplication($userId, $orderId, $goodsId, $couponId, $refundAmount, RefundApplicationInput $input)
    {
        $refundApplication = RefundApplication::new();
        $refundApplication->user_id = $userId;
        $refundApplication->$orderId = $orderId;
        $refundApplication->$goodsId = $goodsId;
        $refundApplication->coupon_id = $couponId;
        $refundApplication->refund_amount = $refundAmount;
        return $this->updateRefundApplication($refundApplication, $input);
    }

    public function updateRefundApplication(RefundApplication $refundApplication, RefundApplicationInput $input)
    {
        if ($refundApplication->status == 2) {
            $refundApplication->status = 0;
            $refundApplication->failure_reason = '';
        }
        $refundApplication->refund_type = $input->type;
        $refundApplication->refund_reason = $input->reason;
        $refundApplication->image_list = json_encode($input->imageList);
        $refundApplication->save();

        return $refundApplication;
    }

    public function getRefundApplicationList(StatusPageInput $input, $columns = ['*'])
    {
        $query = RefundApplication::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRefundApplicationById($id, $columns = ['*'])
    {
        return RefundApplication::query()->find($id, $columns);
    }

    public function getRefundApplicationByUserId($userId, $columns = ['*'])
    {
        return RefundApplication::query()->where('user_id', $userId)->first($columns);
    }

    public function getUserRefundApplication($userId, $id, $columns = ['*'])
    {
        return RefundApplication::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return RefundApplication::query()->whereIn('id', $ids)->get($columns);
    }
}
