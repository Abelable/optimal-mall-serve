<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\CouponService;
use App\Services\GiftGoodsService;
use App\Services\GoodsService;
use App\Services\RuralGoodsService;
use App\Services\RuralRegionService;

class RuralController extends Controller
{
    protected $only = [];

    public function regionOptions()
    {
        $list = RuralRegionService::getInstance()->getRegionOptions(['id', 'name']);
        return $this->success($list);
    }

    public function goodsList()
    {
        $regionId = $this->verifyRequiredId('regionId');
        $goodsIds = RuralGoodsService::getInstance()->getGoodsList($regionId, ['goods_id'])->pluck('goods_id')->toArray();

        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, [0, 1], ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);
        $list = $goodsList->map(function (Goods $goods) use ($giftGoodsIds, $activityList, $groupedCouponList) {
            $activity = $activityList->get($goods->id);
            $goods['activityInfo'] = $activity;

            $couponList = $groupedCouponList->get($goods->id);
            $goods['couponList'] = $couponList ?: [];

            $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;

            return $goods;
        });

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
