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
}
