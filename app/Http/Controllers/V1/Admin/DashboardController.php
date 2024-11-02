<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;

class DashboardController extends Controller
{
    protected $guard = 'Admin';

    public function salesData()
    {
        $totalSales = OrderService::getInstance()->salesSum();
        $dailySalesList = OrderService::getInstance()->dailySalesList();
        $dailyGrowthRate = OrderService::getInstance()->dailySalesGrowthRate();
        $weeklyGrowthRate = OrderService::getInstance()->weeklySalesGrowthRate();

        return $this->success([
            'totalSales' => number_format($totalSales, 2),
            'dailySalesList' => $dailySalesList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate
        ]);
    }

    public function orderCountData()
    {
        $totalCount = OrderService::getInstance()->orderCountSum();
        $dailyCountList = OrderService::getInstance()->dailyOrderCountList();
        $dailyGrowthRate = OrderService::getInstance()->dailyOrderCountGrowthRate();
        $weeklyGrowthRate = OrderService::getInstance()->weeklyOrderCountGrowthRate();

        return $this->success([
            'totalCount' => $totalCount,
            'dailyCountList' => $dailyCountList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate
        ]);
    }
}
