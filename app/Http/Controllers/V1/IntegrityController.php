<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\IntegrityGoods;
use App\Services\ActivityService;
use App\Services\CouponService;
use App\Services\GiftGoodsService;
use App\Services\GoodsService;
use App\Services\IntegrityGoodsService;

class IntegrityController extends Controller
{
    protected $only = [];

    public function goodsList()
    {
        $integrityGoodsList = IntegrityGoodsService::getInstance()->getGoodsList();
        $goodsIds = $integrityGoodsList->pluck('goods_id')->toArray();

        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, [0, 1], ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $integrityGoodsList->map(function (IntegrityGoods $integrityGoods) use ($giftGoodsIds, $activityList, $groupedCouponList, $goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($integrityGoods->goods_id);

            if (!is_null($goods)) {
                $activity = $activityList->get($goods->id);
                $goods['activityInfo'] = $activity;

                $couponList = $groupedCouponList->get($goods->id);
                $goods['couponList'] = $couponList ?: [];

                $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;
            } else {
                IntegrityGoodsService::getInstance()->deleteById($integrityGoods->id);
            }

            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
