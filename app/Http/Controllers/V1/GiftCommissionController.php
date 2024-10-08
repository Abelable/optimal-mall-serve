<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GiftCommissionService;
use App\Services\TeamCommissionService;

class GiftCommissionController extends Controller
{
    public function sum()
    {
        $promoterCashCommission = GiftCommissionService::getInstance()->getPromoterCashCommission($this->userId());
        $managerCashCommission = GiftCommissionService::getInstance()->getManagerCashCommission($this->userId());
        $cashAmount = bcadd($promoterCashCommission, $managerCashCommission, 2);

        $promoterPendingAmount = GiftCommissionService::getInstance()->getPromoterCommissionSum($this->userId(), [1]);
        $managerPendingAmount = GiftCommissionService::getInstance()->getManagerCommissionSum($this->userId(), [1]);
        $pendingAmount = bcadd($promoterPendingAmount, $managerPendingAmount, 2);

        $promoterSettledAmount = GiftCommissionService::getInstance()->getPromoterCommissionSum($this->userId(), [2, 3]);
        $managerSettledAmount = GiftCommissionService::getInstance()->getManagerCommissionSum($this->userId(), [2, 3]);
        $settledAmount = bcadd($promoterSettledAmount, $managerSettledAmount, 2);

        if (!is_null($this->user()->promoterInfo) && $this->user()->promoterInfo->level > 1) {
            $teamCashCommission = TeamCommissionService::getInstance()->getUserCashCommission($this->userId());
            $cashAmount = bcadd($cashAmount, $teamCashCommission, 2);

            $teamPendingCommission = TeamCommissionService::getInstance()->getUserCommission($this->userId(), [1]);
            $pendingAmount = bcadd($pendingAmount, $teamPendingCommission, 2);

            $teamSettledCommission = TeamCommissionService::getInstance()->getUserCommission($this->userId(), [2, 3]);
            $settledAmount = bcadd($settledAmount, $teamSettledCommission, 2);
        }

        return $this->success([
            'cashAmount' => $cashAmount,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function timeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');

        $promoterQuery = GiftCommissionService::getInstance()->getPromoterCommissionQueryByTimeType($this->userId(), $timeType);
        $promoterOrderCount = (clone $promoterQuery)->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $promoterSalesVolume = (clone $promoterQuery)->whereIn('status', [1, 2, 3])->sum('goods_price');
        $promoterPendingAmount = (clone $promoterQuery)->where('status', 1)->sum('promoter_commission');
        $promoterSettledAmount = (clone $promoterQuery)->whereIn('status', [2, 3])->sum('promoter_commission');

        $managerQuery = GiftCommissionService::getInstance()->getManagerCommissionQueryByTimeType($this->userId(), $timeType);
        $managerOrderCount = (clone $managerQuery)->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $managerSalesVolume = (clone $managerQuery)->whereIn('status', [1, 2, 3])->sum('goods_price');
        $managerPendingAmount = (clone $managerQuery)->where('status', 1)->sum('manager_commission');
        $managerSettledAmount = (clone $managerQuery)->whereIn('status', [2, 3])->sum('manager_commission');

        return $this->success([
            'orderCount' => $promoterOrderCount + $managerOrderCount,
            'salesVolume' => bcadd($promoterSalesVolume, $managerSalesVolume, 2),
            'pendingAmount' => bcadd($promoterPendingAmount, $managerPendingAmount, 2),
            'settledAmount' => bcadd($promoterSettledAmount, $managerSettledAmount, 2)
        ]);
    }

    public function cash()
    {
        [$cashGiftCommission, $cashTeamCommission] = GiftCommissionService::getInstance()->cash($this->userId());
        return $this->success([
            'share' => $cashGiftCommission,
            'team' => bcadd($cashTeamCommission, 0, 2)
        ]);
    }
}
