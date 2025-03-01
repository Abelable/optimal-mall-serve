<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\PromoterService;
use App\Services\UserService;

class DashboardController extends Controller
{
    protected $guard = 'Admin';

    public function salesData()
    {
        $totalSales = OrderService::getInstance()->salesSum();
        $dailySalesList = OrderService::getInstance()->dailySalesList();
        $dailyGrowthRate = OrderService::getInstance()->dailySalesGrowthRate();
        $weeklyGrowthRate = OrderService::getInstance()->weeklySalesGrowthRate();
        $monthlySalesList = OrderService::getInstance()->monthlySalesList();

        return $this->success([
            'totalSales' => number_format($totalSales, 2),
            'dailySalesList' => $dailySalesList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate,
            'monthlySalesList' => $monthlySalesList,
        ]);
    }

    public function orderCountData()
    {
        $totalCount = OrderService::getInstance()->orderCountSum();
        $dailyCountList = OrderService::getInstance()->dailyOrderCountList();
        $dailyGrowthRate = OrderService::getInstance()->dailyOrderCountGrowthRate();
        $weeklyGrowthRate = OrderService::getInstance()->weeklyOrderCountGrowthRate();

        $repeatCustomersCount = OrderService::getInstance()->repeatCustomersCount();
        $usersWithOrdersCount = OrderService::getInstance()->usersWithOrdersCount();

        $repurchaseRate = 0;
        if ($usersWithOrdersCount > 0) {
            $repurchaseRate = ($repeatCustomersCount / $usersWithOrdersCount) * 100;
        }

        return $this->success([
            'totalCount' => $totalCount,
            'dailyCountList' => $dailyCountList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate,
            'repurchaseRate' => round($repurchaseRate, 2)
        ]);
    }

    public function userCountData()
    {
        $totalCount = UserService::getInstance()->userCountSum();
        $dailyCountList = UserService::getInstance()->dailyUserCountList();
        $dailyGrowthRate = UserService::getInstance()->dailyUserCountGrowthRate();
        $weeklyGrowthRate = UserService::getInstance()->weeklyUserCountGrowthRate();

        $usersWithOrdersCount = OrderService::getInstance()->usersWithOrdersCount();
        $orderRate = 0;
        if ($totalCount > 0) {
            $orderRate = ($usersWithOrdersCount / $totalCount) * 100;
        }

        return $this->success([
            'totalCount' => $totalCount,
            'dailyCountList' => $dailyCountList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate,
            'orderRate' => round($orderRate, 2)
        ]);
    }

    public function promoterCountData()
    {
        $totalCount = PromoterService::getInstance()->promoterCountSum();
        $dailyCountList = PromoterService::getInstance()->dailyPromoterCountList();
        $dailyGrowthRate = PromoterService::getInstance()->dailyPromoterCountGrowthRate();
        $weeklyGrowthRate = PromoterService::getInstance()->weeklyPromoterCountGrowthRate();

        return $this->success([
            'totalCount' => $totalCount,
            'dailyCountList' => $dailyCountList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate
        ]);
    }
}
