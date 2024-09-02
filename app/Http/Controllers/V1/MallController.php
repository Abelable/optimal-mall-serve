<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\CouponService;
use App\Services\GoodsService;
use App\Services\MallBannerService;

class MallController extends Controller
{
    protected $only = [];

    public function bannerList()
    {
        $list = MallBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

    public function activityList()
    {
        $tag = $this->verifyRequiredInteger('tag');
        $columns = ['tag', 'name', 'goods_id', 'goods_tag', 'start_time', 'end_time', 'followers', 'sales'];
        $activityList = ActivityService::getInstance()->getActivityList($tag, $columns);
        $activityKeyList = $activityList->keyBy('goods_id');

        $goodsIds = $activityList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $list = $goodsList->map(function (Goods $goods) use ($activityKeyList, $groupedCouponList) {
            /** @var Activity $activity */
            $activity = $activityKeyList->get($goods->id);
            $goods['tag'] = $activity->goods_tag;

            unset($activity->goods_id);
            unset($activity->goods_tag);
            $goods['activityInfo'] = $activity;

            $couponList = $groupedCouponList->get($goods->id);
            $goods['couponList'] = $couponList ?: [];

            return $goods;
        });

        // todo 缓存活动商品列表

        return $this->success($list);
    }
}
