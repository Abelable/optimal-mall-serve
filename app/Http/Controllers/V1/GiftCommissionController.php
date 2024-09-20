<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GiftCommissionService;
use Illuminate\Support\Carbon;

class GiftCommissionController extends Controller
{
    public function sum()
    {
        $promoterCashCommission = GiftCommissionService::getInstance()->getPromoterCashCommission($this->userId());
        $managerCashCommission = GiftCommissionService::getInstance()->getManagerCashCommission($this->userId());
        $cashAmount = bcadd($promoterCashCommission, $managerCashCommission, 2);

        $promoterPendingAmount = GiftCommissionService::getInstance()->getPromoterCommissionSum($this->userId(), 1);
        $managerPendingAmount = GiftCommissionService::getInstance()->getManagerCommissionSum($this->userId(), 1);
        $pendingAmount = bcadd($promoterPendingAmount, $managerPendingAmount, 2);

        $promoterSettledAmount = GiftCommissionService::getInstance()->getPromoterCommissionSum($this->userId(), 3);
        $managerSettledAmount = GiftCommissionService::getInstance()->getManagerCommissionSum($this->userId(), 3);
        $settledAmount = bcadd($promoterSettledAmount, $managerSettledAmount, 2);

        return $this->success([
            'cashAmount' => $cashAmount,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function timeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');
        $scene = $this->verifyInteger('scene');

        $query = GiftCommissionService::getInstance()->getUserCommissionQueryByTimeType($this->userId(), $timeType, $scene);
        $orderCount = $query->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $salesVolume = $query->whereIn('status', [1, 2, 3])->sum('payment_amount');
        $pendingAmount = $query->where('status', 1)->sum('commission_amount');
        $settledAmount = $query->where('status', 3)->sum('commission_amount');

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function cash()
    {
        $selfPurchase = GiftCommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), 2)
            ->whereMonth('updated_at', '!=', Carbon::now()->month)
            ->where('scene', 1)
            ->sum('commission_amount');
        $share = GiftCommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), 2)
            ->whereMonth('updated_at', '!=', Carbon::now()->month)
            ->where('scene', 2)
            ->sum('commission_amount');

        return $this->success([
            'selfPurchase' => $selfPurchase,
            'share' => $share
        ]);
    }
}
