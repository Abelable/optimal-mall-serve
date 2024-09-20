<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use Illuminate\Support\Carbon;

class CommissionController extends Controller
{
    public function sum()
    {
        $cashAmount = CommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), 2)
            ->whereMonth('updated_at', '!=', Carbon::now()->month)
            ->sum('commission_amount');
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
        $scene = $this->verifyInteger('scene');

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType($this->userId(), $timeType, $scene);
        $orderCount = $query->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $salesVolume = $query->whereIn('status', [1, 2, 3])->sum('commission_base');
        $pendingAmount = $query->where('status', 1)->sum('commission_amount');
        $settledAmount = $query->where('status', 3)->sum('commission_amount');

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

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType($this->userId(), $timeType);
        $orderCount = $query->whereIn('status', [1, 2, 3])->distinct('order_id')->count('order_id');
        $salesVolume = $query->whereIn('status', [1, 2, 3])->sum('commission_base');

        $pendingAmount = 0;
        $settledAmount = 0;
        if (!is_null($this->user()->promoterInfo)) {
            $pendingGMV = $query->where('status', 1)->sum('commission_base');
            $settledGMV = $query->where('status', 3)->sum('commission_base');
            switch ($this->user()->promoterInfo->level) {
                case 1:
                    $pendingAmount = bcmul($pendingGMV, 0.01, 2);
                    $settledAmount = bcmul($settledGMV, 0.01, 2);
                    break;
                case 2:
                    $pendingAmount = bcmul($pendingGMV, 0.02, 2);
                    $settledAmount = bcmul($settledGMV, 0.02, 2);
                    break;
                case 3:
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
        $selfPurchase = CommissionService::getInstance()
            ->getUserCommissionQuery($this->userId(), 2)
            ->whereMonth('updated_at', '!=', Carbon::now()->month)
            ->where('scene', 1)
            ->sum('commission_amount');
        $share = CommissionService::getInstance()
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
