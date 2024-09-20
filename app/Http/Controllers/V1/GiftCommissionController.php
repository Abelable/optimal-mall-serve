<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use App\Services\GiftCommissionService;
use Illuminate\Support\Carbon;

class GiftCommissionController extends Controller
{
    public function sum()
    {
        $promoterCashCommission = GiftCommissionService::getInstance()->getPromoterCashCommission($this->userId());
        $managerCashCommission = GiftCommissionService::getInstance()->getManagerCashCommission($this->userId());
        $cashAmount = bcadd($promoterCashCommission, $managerCashCommission, 2);
        if (!is_null($this->user()->promoterInfo)) {
            $cashGMV = CommissionService::getInstance()
                ->getUserCommissionQuery($this->userId(), 2)
                ->whereMonth('updated_at', '!=', Carbon::now()->month)
                ->sum('commission_base');
            switch ($this->user()->promoterInfo->level) {
                case 1:
                    $teamCashCommission = bcmul($cashGMV, 0.01, 2);
                    $cashAmount = bcadd($cashAmount, $teamCashCommission, 2);
                    break;
                case 2:
                    $teamCashCommission = bcmul($cashGMV, 0.02, 2);
                    $cashAmount = bcadd($cashAmount, $teamCashCommission, 2);
                    break;
                case 3:
                    $teamCashCommission = bcmul($cashGMV, 0.03, 2);
                    $cashAmount = bcadd($cashAmount, $teamCashCommission, 2);
                    break;
            }
        }

        $promoterPendingAmount = GiftCommissionService::getInstance()->getPromoterCommissionSum($this->userId(), 1);
        $managerPendingAmount = GiftCommissionService::getInstance()->getManagerCommissionSum($this->userId(), 1);
        $pendingAmount = bcadd($promoterPendingAmount, $managerPendingAmount, 2);
        if (!is_null($this->user()->promoterInfo)) {
            $pendingGMV = CommissionService::getInstance()->getUserGMV($this->userId(), 1);
            switch ($this->user()->promoterInfo->level) {
                case 1:
                    $teamPendingCommission = bcmul($pendingGMV, 0.01, 2);
                    $pendingAmount = bcadd($pendingAmount, $teamPendingCommission, 2);
                    break;
                case 2:
                    $teamPendingCommission = bcmul($pendingGMV, 0.02, 2);
                    $pendingAmount = bcadd($pendingAmount, $teamPendingCommission, 2);
                    break;
                case 3:
                    $teamPendingCommission = bcmul($pendingGMV, 0.03, 2);
                    $pendingAmount = bcadd($pendingAmount, $teamPendingCommission, 2);
                    break;
            }
        }

        $promoterSettledAmount = GiftCommissionService::getInstance()->getPromoterCommissionSum($this->userId(), 3);
        $managerSettledAmount = GiftCommissionService::getInstance()->getManagerCommissionSum($this->userId(), 3);
        $settledAmount = bcadd($promoterSettledAmount, $managerSettledAmount, 2);
        if (!is_null($this->user()->promoterInfo)) {
            $settledGMV = CommissionService::getInstance()->getUserGMV($this->userId(), 3);
            switch ($this->user()->promoterInfo->level) {
                case 1:
                    $teamSettledCommission = bcmul($settledGMV, 0.01, 2);
                    $settledAmount = bcadd($settledAmount, $teamSettledCommission, 2);
                    break;
                case 2:
                    $teamSettledCommission = bcmul($settledGMV, 0.02, 2);
                    $settledAmount = bcadd($settledAmount, $teamSettledCommission, 2);
                    break;
                case 3:
                    $teamSettledCommission = bcmul($settledGMV, 0.03, 2);
                    $settledAmount = bcadd($settledAmount, $teamSettledCommission, 2);
                    break;
            }
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
        $promoterCashGiftCommission = GiftCommissionService::getInstance()->getPromoterCashCommission($this->userId());
        $managerCashGiftCommission = GiftCommissionService::getInstance()->getManagerCashCommission($this->userId());
        $cashGiftCommission = bcadd($promoterCashGiftCommission, $managerCashGiftCommission, 2);

        $cashTeamCommission = 0;
        if (!is_null($this->user()->promoterInfo)) {
            $cashGMV = CommissionService::getInstance()
                ->getUserCommissionQuery($this->userId(), 2)
                ->whereMonth('updated_at', '!=', Carbon::now()->month)
                ->sum('commission_base');
            switch ($this->user()->promoterInfo->level) {
                case 1:
                    $cashTeamCommission = bcmul($cashGMV, 0.01, 2);
                    break;
                case 2:
                    $cashTeamCommission = bcmul($cashGMV, 0.02, 2);
                    break;
                case 3:
                    $cashTeamCommission = bcmul($cashGMV, 0.03, 2);
                    break;
            }
        }

        return $this->success([
            'share' => $cashGiftCommission,
            'team' => $cashTeamCommission
        ]);
    }
}
