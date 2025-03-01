<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderGoods;
use App\Services\GoodsService;
use App\Services\OrderGoodsService;
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
        $monthlySalesList = OrderService::getInstance()->monthlySalesList();
        $dailyGrowthRate = OrderService::getInstance()->dailySalesGrowthRate();
        $weeklyGrowthRate = OrderService::getInstance()->weeklySalesGrowthRate();

        return $this->success([
            'totalSales' => number_format($totalSales, 2),
            'dailySalesList' => $dailySalesList,
            'monthlySalesList' => $monthlySalesList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate,
        ]);
    }

    public function orderCountData()
    {
        $totalCount = OrderService::getInstance()->orderCountSum();
        $dailyCountList = OrderService::getInstance()->dailyOrderCountList();
        $monthlyCountList = OrderService::getInstance()->monthlyOrderCountList();
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
            'monthlyCountList' => $monthlyCountList,
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

    public function topGoodsList()
    {
        $startDate = $this->verifyRequiredString('startDate');
        $endDate = $this->verifyRequiredString('endDate');

        $topSalesList = OrderGoodsService::getInstance()->getTopSalesGoodsList($startDate, $endDate);
        $topOrderCountList = OrderGoodsService::getInstance()->getTopOrderCountGoodsList($startDate, $endDate);

        $topSalesGoodsIds = $topSalesList->pluck('goods_id')->toArray();
        $topOrderCountGoodsIds = $topOrderCountList->pluck('goods_id')->toArray();
        $goodsIds = array_unique(array_merge($topSalesGoodsIds, $topOrderCountGoodsIds));
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $topSalesGoodsList = $topSalesList->map(function ($item) use ($goodsList) {
            $goods = $goodsList->get($item->goods_id);
            return [
                'id' => $goods->id,
                'cover' => $goods->cover,
                'name' => $goods->name,
                'sum' => $item->sum,
            ];
        });

        $topOrderCountGoodsList = $topOrderCountList->map(function ($item) use ($goodsList) {
            $goods = $goodsList->get($item->goods_id);
            return [
                'id' => $goods->id,
                'cover' => $goods->cover,
                'name' => $goods->name,
                'count' => $item->count,
            ];
        });

        return $this->success([
            'topSalesGoodsList' => $topSalesGoodsList,
            'topOrderCountGoodsList' => $topOrderCountGoodsList
        ]);
    }
}
