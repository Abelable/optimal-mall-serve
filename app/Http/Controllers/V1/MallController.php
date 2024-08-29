<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AdvanceGoods;
use App\Models\Goods;
use App\Models\TodayGoods;
use App\Services\ActivityService;
use App\Services\AdvanceGoodsService;
use App\Services\GoodsService;
use App\Services\MallBannerService;
use App\Services\TodayGoodsService;

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
        $status = $this->verifyRequiredInteger('status');
        $columns = ['status', 'name', 'goods_id', 'goods_type', 'start_time', 'end_time', 'followers', 'sales'];
        $activityList = ActivityService::getInstance()->getActivityList($status, $columns);
        $activityKeyList = $activityList->keyBy('goods_id');

        $goodsIds = $activityList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);

        $list = $goodsList->map(function (Goods $goods) use ($activityKeyList) {
            /** @var Activity $activity */
            $activity = $activityKeyList->get($goods->id);
            $goods['type'] = $activity->goods_type;

            unset($activity->goods_id);
            unset($activity->goods_type);
            $goods['activityInfo'] = $activity;
            return $goods;
        });

        // todo 缓存活动商品列表

        return $this->success($list);
    }

    public function todayGoodsList()
    {
        $todayGoodsList = TodayGoodsService::getInstance()->getGoodsList();
        $typeList = $todayGoodsList->keyBy('goods_id');
        $goodsIds = $todayGoodsList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);
        $list = $goodsList->map(function (Goods $goods) use ($typeList) {
            /** @var TodayGoods $todayGoods */
            $todayGoods = $typeList->get($goods->id);
            $goods['type'] = $todayGoods->type;
            return $goods;
        });

        // todo 商品列表存缓存

        return $this->success($list);
    }

    public function advanceGoodsList()
    {
        $advanceGoodsList = AdvanceGoodsService::getInstance()->getGoodsList();
        $typeList = $advanceGoodsList->keyBy('goods_id');
        $goodsIds = $advanceGoodsList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);
        $list = $goodsList->map(function (Goods $goods) use ($typeList) {
            /** @var AdvanceGoods $advanceGoods */
            $advanceGoods = $typeList->get($goods->id);
            $goods['type'] = $advanceGoods->type;
            return $goods;
        });

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
