<?php

namespace App\Services;

use App\Models\OrderVerify;

class OrderVerifyService extends BaseService
{
    public function createOrderVerify($orderId)
    {
        $verify = OrderVerify::new();
        $verify->order_id = $orderId;
        $verify->verify_code = OrderVerify::generateVerifyCode();
        $verify->save();
        return $verify;
    }

    public function getByCode($code, $columns = ['*'])
    {
        return OrderVerify::query()->where('verify_code', $code)->where('status', 0)->first($columns);
    }

    public function getByOrderId($orderId, $columns = ['*'])
    {
        return OrderVerify::query()->where('order_id', $orderId)->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return OrderVerify::query()->find($id, $columns);
    }

    public function verified($id, $userId)
    {
        $verify = OrderVerify::query()->find($id);
        $verify->status = 1;
        $verify->verifier_id = $userId;
        $verify->save();
        return $verify;
    }
}
