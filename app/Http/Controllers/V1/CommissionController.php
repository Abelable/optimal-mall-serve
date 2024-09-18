<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Services\CommissionService;
use App\Services\OrderService;

class CommissionController extends Controller
{
    public function sum()
    {
        $cashAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), 2);
        $pendingAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), 1);
        $settledAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), 3);
        return $this->success([
            'cashAmount' => $cashAmount,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function timeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType($this->userId(), $timeType);
        $orderCount = $query->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $salesVolume = $query->whereIn('status', [1, 2, 3])->sum('payment_amount');
        $pendingAmount = $query->where('status', 1)->sum('commission');
        $settledAmount = $query->where('status', 3)->sum('commission');

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }
//
//    public function timeOrderList()
//    {
//        /** @var CommissionOrderPageInput $input */
//        $input = CommissionOrderPageInput::new();
//        $page = CommissionService::getInstance()->getUserTimePage($this->userId(), $input);
//        $commissionList = collect($page->items());
//
//        $orderIds = $commissionList->pluck('order_id')->toArray();
//        $orderList = OrderService::getInstance()->getOrderListByIds($orderIds);
//
////        $list = $commissionList->map(function (Commission $commission) use ($orderList) {
////            /** @var Order $order */
////            $order = $orderList->get($commission->order_id);
////            return [
////                'id' => $commission->id,
////                'orderSn' => $order->order_sn,
////                'status' => $commission->status,
////                'createdAt' => $order->created_at,
////                'commission' => $commission->commission,
////                'scene' => $commission->scene,
////
////            ];
////        });
//
//        return $this->success($this->paginate($page, $list));
//    }

    public function cash()
    {
        $selfPurchase = CommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), 2)
            ->where('scene', 1)
            ->sum('commission');
        $share = CommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), 2)
            ->where('scene', 2)
            ->sum('commission');

        return $this->success([
            'selfPurchase' => $selfPurchase,
            'share' => $share
        ]);
    }
}
