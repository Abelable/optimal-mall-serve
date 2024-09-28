<?php

namespace App\Services;

use App\Models\Refund;
use App\Utils\Inputs\RefundInput;
use App\Utils\Inputs\StatusPageInput;

class RefundService extends BaseService
{
    public function createRefund($userId, $orderId, $orderSn, $goodsId, $couponId, $refundAmount, RefundInput $input)
    {
        $refund = Refund::new();
        $refund->user_id = $userId;
        $refund->order_id = $orderId;
        $refund->order_sn = $orderSn;
        $refund->goods_id = $goodsId;
        $refund->coupon_id = $couponId;
        $refund->refund_amount = $refundAmount;
        return $this->updateRefund($refund, $input);
    }

    public function updateRefund(Refund $refund, RefundInput $input)
    {
        if ($refund->status == 2) {
            $refund->status = 0;
            $refund->failure_reason = '';
        }
        $refund->refund_type = $input->type;
        $refund->refund_reason = $input->reason;
        $refund->image_list = json_encode($input->imageList);
        $refund->save();

        return $refund;
    }

    public function getRefundList(StatusPageInput $input, $columns = ['*'])
    {
        $query = Refund::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRefundById($id, $columns = ['*'])
    {
        return Refund::query()->find($id, $columns);
    }

    public function getRefundByUserId($userId, $orderId, $goodsId, $columns = ['*'])
    {
        return Refund::query()
            ->where('user_id', $userId)
            ->where('order_id', $orderId)
            ->where('goods_id', $goodsId)
            ->first($columns);
    }

    public function getUserRefund($userId, $id, $columns = ['*'])
    {
        return Refund::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return Refund::query()->whereIn('id', $ids)->get($columns);
    }
}
