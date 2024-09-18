<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;

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
