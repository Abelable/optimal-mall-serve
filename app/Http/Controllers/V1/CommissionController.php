<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Utils\CodeResponse;
use Illuminate\Support\Carbon;

class CommissionController extends Controller
{
    public function sum()
    {
        $cashAmount = CommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('commission_amount');
        $pendingAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), [1]);
        $settledAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), [2, 3]);
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

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType([$this->userId()], $timeType, $scene);

        $orderCount = (clone $query)->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3])->sum('commission_base');
        $pendingAmount = (clone $query)->where('status', 1)->sum('commission_amount');
        $settledAmount = (clone $query)->whereIn('status', [2, 3])->sum('commission_amount');

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function teamTimeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');

        $customerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $promoterIds = PromoterService::getInstance()->getPromoterListByUserIds($customerIds)->pluck('user_id')->toArray();

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType($promoterIds, $timeType);
        $orderCount = (clone $query)->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3])->sum('commission_base');

        $pendingAmount = 0;
        $settledAmount = 0;
        if (!is_null($this->user()->promoterInfo)) {
            $pendingGMV = (clone $query)->where('status', 1)->sum('commission_base');
            $settledGMV = (clone $query)->whereIn('status', [2, 3])->sum('commission_base');
            switch ($this->user()->promoterInfo->level) {
                case 2:
                    $pendingAmount = bcmul($pendingGMV, 0.01, 2);
                    $settledAmount = bcmul($settledGMV, 0.01, 2);
                    break;
                case 3:
                    $pendingAmount = bcmul($pendingGMV, 0.02, 2);
                    $settledAmount = bcmul($settledGMV, 0.02, 2);
                    break;
                case 4:
                    $pendingAmount = bcmul($pendingGMV, 0.03, 2);
                    $settledAmount = bcmul($settledGMV, 0.03, 2);
                    break;
            }
        }

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function cash()
    {
        $commissionQuery = CommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month);
        $selfPurchase = $commissionQuery->where('scene', 1)->sum('commission_amount');
        $share = $commissionQuery->where('scene', 2)->sum('commission_amount');
        return $this->success([
            'selfPurchase' => $selfPurchase,
            'share' => $share
        ]);
    }

    public function achievement()
    {
        if (is_null($this->user()->promoterInfo)) {
            return $this->fail(CodeResponse::FAIL, '非推广员无法查看数据');
        }

        $beforeLastMonthGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 5);
        $lastMonthGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 4);
        $curMonthGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 3);
        $percent = 0;

        // 推广员升C1：3个月累计超3w
        // C1升C2：3个月累计超50w
        // C2生C3：3个月每个月60w
        $level = $this->user()->promoterInfo->level;
        switch ($level) {
            case 1:
                $totalGMV = bcadd($beforeLastMonthGMV, $lastMonthGMV, 2);
                $totalGMV = bcadd($totalGMV, $curMonthGMV, 2);
                $target = 30000;
                if ($totalGMV >= $target) {
                    $percent = 100;
                } else {
                    $percent = round(($totalGMV / $target) * 100, 2);
                }
                break;
            case 2:
                $totalGMV = bcadd($beforeLastMonthGMV, $lastMonthGMV, 2);
                $totalGMV = bcadd($totalGMV, $curMonthGMV, 2);
                $target = 500000;
                if ($totalGMV >= $target) {
                    $percent = 100;
                } else {
                    $percent = round(($totalGMV / $target) * 100, 2);
                }
                break;
            case 3:
                $perMonthTarget = 600000;
                if ($beforeLastMonthGMV >= $perMonthTarget && $lastMonthGMV >= $perMonthTarget) {
                    if ($curMonthGMV >= $perMonthTarget) {
                        $percent = 100;
                    } else {
                        $totalGMV = bcadd($beforeLastMonthGMV, $lastMonthGMV, 2);
                        $totalGMV = bcadd($totalGMV, $curMonthGMV, 2);
                        $percent = round(($totalGMV / $perMonthTarget * 3) * 100, 2);
                    }
                } else if ($lastMonthGMV >= $perMonthTarget) {
                    $totalGMV = bcadd($lastMonthGMV, $curMonthGMV, 2);
                    $percent = round(($totalGMV / $perMonthTarget * 3) * 100, 2);
                } else {
                    $percent = round(($curMonthGMV / $perMonthTarget * 3) * 100, 2);
                }
        }

        return $this->success([
            'beforeLastMonthGMV' => $beforeLastMonthGMV,
            'lastMonthGMV' => $lastMonthGMV,
            'curMonthGMV' => $curMonthGMV,
            'percent' => $percent
        ]);
    }
}
