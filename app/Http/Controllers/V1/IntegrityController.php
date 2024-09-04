<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\CouponService;
use App\Services\GoodsService;
use App\Services\IntegrityBannerService;
use App\Services\IntegrityGoodsService;

class IntegrityController extends Controller
{
    protected $only = [];

    public function bannerList()
    {
        $list = IntegrityBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

    public function goodsList()
    {
        $goodsIds = IntegrityGoodsService::getInstance()->getGoodsList(['goods_id'])->pluck('goods_id')->toArray();
        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');
        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);
        $list = $goodsList->map(function (Goods $goods) use ($activityList, $groupedCouponList) {
            $activity = $activityList->get($goods->id);
            $goods['activityInfo'] = $activity;

            $couponList = $groupedCouponList->get($goods->id);
            $goods['couponList'] = $couponList ?: [];

            return $goods;
        });

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
